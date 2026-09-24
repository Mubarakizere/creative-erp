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
use App\Models\Company;

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
        $user = auth()->user();
        $companyId = session('company_id') ?? $user->company_id;

        $query = Invoice::with(['client', 'project.company', 'company']);
        
        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('display_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('project_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function ($comq) use ($search) {
                      $comq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $invoices = $query->latest()->paginate(15)->withQueryString();
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('admin.finance.invoices.index', compact('invoices', 'companies'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Invoice::class);
        $user = auth()->user();
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $defaultCompanyId = session('company_id') ?? $user->company_id ?? $companies->first()?->id ?? 1;

        // Handle creating from quotation
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = Quotation::with('items')->findOrFail($request->quotation_id);
            if ($quotation->company_id) {
                $defaultCompanyId = $quotation->company_id;
            }
        }

        $clients = Client::where('status', 'active')->orWhereNull('status')->orderBy('company_name')->orderBy('first_name')->get();
        $projects = Project::where('status', '!=', 'Closed')->with(['company:id,name', 'client:id,display_name,first_name,last_name,company_name'])->orderBy('name')->get();

        $projectsData = $projects->mapWithKeys(function ($p) {
            return [
                $p->id => [
                    'id' => $p->id,
                    'company_id' => $p->company_id,
                    'client_id' => $p->client_id,
                ]
            ];
        })->toArray();

        $invoice_number = app(\App\Services\SequenceService::class)->generate('invoice', $defaultCompanyId);

        return view('admin.finance.invoices.create', compact('clients', 'projects', 'companies', 'projectsData', 'quotation', 'invoice_number', 'defaultCompanyId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Invoice::class);
        
        if (!$request->filled('company_id')) {
            $request->merge(['company_id' => auth()->user()->company_id ?? 1]);
        }

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = $request->except('items');
        $items = $request->input('items', []);
        
        $invoice = $this->invoiceService->createInvoice($data, $items);
        
        return redirect()->route('admin.finance.invoices.show', $invoice)
                         ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['items', 'client', 'project', 'company', 'allocations.payment']);
        return view('admin.finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'Draft') {
            return redirect()->route('admin.finance.invoices.show', $invoice)
                             ->with('error', 'Only Draft invoices can be edited.');
        }

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $clients = Client::where('status', 'active')->orWhereNull('status')->orderBy('company_name')->orderBy('first_name')->get();
        $projects = Project::where('status', '!=', 'Closed')->with(['company:id,name', 'client:id,display_name,first_name,last_name,company_name'])->orderBy('name')->get();
        
        $projectsData = $projects->mapWithKeys(function ($p) {
            return [
                $p->id => [
                    'id' => $p->id,
                    'company_id' => $p->company_id,
                    'client_id' => $p->client_id,
                ]
            ];
        })->toArray();

        $invoice->load('items', 'company', 'client', 'project');

        return view('admin.finance.invoices.edit', compact('invoice', 'companies', 'clients', 'projects', 'projectsData'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'Draft') {
            return redirect()->route('admin.finance.invoices.show', $invoice)
                             ->with('error', 'Only Draft invoices can be edited.');
        }

        if (!$request->filled('company_id')) {
            $request->merge(['company_id' => $invoice->company_id ?? auth()->user()->company_id ?? 1]);
        }

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
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
