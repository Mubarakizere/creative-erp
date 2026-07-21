<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Services\Finance\CreditNoteService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;

class CreditNoteController extends Controller
{
    use AuthorizesRequests;

    protected CreditNoteService $creditNoteService;

    public function __construct(CreditNoteService $creditNoteService)
    {
        $this->creditNoteService = $creditNoteService;
    }

    public function index()
    {
        $this->authorize('viewAny', CreditNote::class);
        $creditNotes = CreditNote::with(['client', 'invoice'])->where('company_id', auth()->user()->company_id ?? 1)->latest()->paginate(15);
        return view('admin.finance.credit-notes.index', compact('creditNotes'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', CreditNote::class);
        $clients = Client::where('company_id', auth()->user()->company_id ?? 1)->get();
        
        $preselectedInvoice = null;
        if ($request->has('invoice_id')) {
            $preselectedInvoice = Invoice::findOrFail($request->invoice_id);
        }
        
        return view('admin.finance.credit-notes.create', compact('clients', 'preselectedInvoice'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', CreditNote::class);
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'reason' => 'required|string',
            'invoice_id' => 'nullable|exists:invoices,id'
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id ?? 1;
        
        $creditNote = $this->creditNoteService->createCreditNote($data);
        
        return redirect()->route('admin.finance.credit-notes.show', $creditNote)
                         ->with('success', 'Credit Note created successfully.');
    }

    public function apply(Request $request, CreditNote $creditNote)
    {
        $this->authorize('update', $creditNote);
        
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01|max:'.$creditNote->remaining_balance
        ]);

        $invoice = Invoice::findOrFail($request->input('invoice_id'));
        $amount = $request->input('amount');
        
        if ($invoice->client_id !== $creditNote->client_id) {
            return redirect()->back()->with('error', 'Invoice must belong to the same client.');
        }

        $this->creditNoteService->applyToInvoice($creditNote, $invoice, $amount);
        
        return redirect()->back()->with('success', 'Credit note applied successfully.');
    }

    public function show(CreditNote $creditNote)
    {
        $this->authorize('view', $creditNote);
        $creditNote->load(['client', 'invoice']);
        
        // Fetch open invoices for this client to apply the credit note
        $openInvoices = Invoice::where('client_id', $creditNote->client_id)
                               ->where('balance_due', '>', 0)
                               ->whereNotIn('status', ['Draft', 'Cancelled'])
                               ->get();

        return view('admin.finance.credit-notes.show', compact('creditNote', 'openInvoices'));
    }
}
