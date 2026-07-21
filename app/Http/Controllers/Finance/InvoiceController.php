<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Finance\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;
use App\Models\Project;
use App\Models\Opportunity;
use App\Models\Quotation;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalService;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);
        $query = Invoice::with(['client', 'project'])->where('company_id', auth()->user()->company_id ?? 1);
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $invoices = $query->latest()->paginate(15);
        return view('admin.finance.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Invoice::class);
        $clients = Client::where('company_id', auth()->user()->company_id ?? 1)->get();
        $projects = Project::where('company_id', auth()->user()->company_id ?? 1)->get();
        
        // Handle creating from quotation
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = Quotation::with('items')->findOrFail($request->quotation_id);
        }

        return view('admin.finance.invoices.create', compact('clients', 'projects', 'quotation'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Invoice::class);
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = $request->except('items');
        $data['company_id'] = auth()->user()->company_id ?? 1;
        $items = $request->input('items', []);
        
        $invoice = $this->invoiceService->createInvoice($data, $items);
        
        return redirect()->route('admin.finance.invoices.show', $invoice)
                         ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['items', 'client', 'project', 'allocations.payment']);
        return view('admin.finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'Draft') {
            return redirect()->route('admin.finance.invoices.show', $invoice)
                             ->with('error', 'Only Draft invoices can be edited.');
        }

        $clients = Client::where('company_id', auth()->user()->company_id ?? 1)->get();
        $projects = Project::where('company_id', auth()->user()->company_id ?? 1)->get();
        $invoice->load('items');

        return view('admin.finance.invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'Draft') {
            return redirect()->route('admin.finance.invoices.show', $invoice)
                             ->with('error', 'Only Draft invoices can be edited.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Delete old items and recreate (simple update strategy)
        $invoice->items()->delete();
        $invoice->update($request->except('items'));
        
        $items = collect($request->input('items'))->map(function($item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            return $item;
        });
        
        $invoice->items()->createMany($items->toArray());
        
        // Subtotal etc should ideally be recalculated by the service but for simplicity we do it here
        $subtotal = $items->sum('total');
        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
            'balance_due' => $subtotal
        ]);

        return redirect()->route('admin.finance.invoices.show', $invoice)
                         ->with('success', 'Invoice updated successfully.');
    }

    public function cancel(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $this->invoiceService->cancel($invoice);
        return redirect()->back()->with('success', 'Invoice cancelled successfully.');
    }

    public function issue(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status === 'Draft') {
            $workflow = ApprovalWorkflow::where('module', 'Invoice')
                ->where('company_id', $invoice->company_id)
                ->where('is_active', true)
                ->first();

            if ($workflow) {
                $invoice->update(['status' => 'Pending Approval']);
                app(ApprovalService::class)->submit($invoice, $workflow, 'Automatically submitted for approval upon issuing');
                return redirect()->back()->with('success', 'Invoice has been submitted for approval.');
            } else {
                $invoice->update(['status' => 'Issued']);
                return redirect()->back()->with('success', 'Invoice has been issued successfully.');
            }
        }
        return redirect()->back()->with('error', 'Only Draft invoices can be issued.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('admin.finance.invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
