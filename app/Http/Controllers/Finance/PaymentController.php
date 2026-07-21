<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Finance\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\BankAccount;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $this->authorize('viewAny', Payment::class);
        $payments = Payment::with(['client', 'paymentMethod'])->where('company_id', auth()->user()->company_id ?? 1)->latest()->paginate(15);
        return view('admin.finance.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Payment::class);
        $clients = Client::where('company_id', auth()->user()->company_id ?? 1)->get();
        $paymentMethods = PaymentMethod::all();
        $bankAccounts = BankAccount::where('company_id', auth()->user()->company_id ?? 1)->get();
        
        $preselectedInvoice = null;
        if ($request->has('invoice_id')) {
            $preselectedInvoice = Invoice::with('client')->findOrFail($request->invoice_id);
            if ($preselectedInvoice->balance_due <= 0) {
                return redirect()->route('admin.finance.invoices.show', $preselectedInvoice)
                                 ->with('error', 'This invoice is already fully paid.');
            }
        }

        // Fetch open invoices for dropdown/selection via JS if needed, or we can just fetch all open invoices for the company
        $openInvoices = Invoice::where('company_id', auth()->user()->company_id ?? 1)
                               ->where('balance_due', '>', 0)
                               ->whereNotIn('status', ['Draft', 'Cancelled'])
                               ->get();

        return view('admin.finance.payments.create', compact('clients', 'paymentMethods', 'bankAccounts', 'preselectedInvoice', 'openInvoices'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Payment::class);
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'allocations' => 'required|array|min:1',
            'allocations.*.invoice_id' => 'required|exists:invoices,id',
            'allocations.*.amount' => 'required|numeric|min:0.01',
        ]);

        $data = $request->except('allocations');
        $data['company_id'] = auth()->user()->company_id ?? 1;
        $allocations = $request->input('allocations', []);
        
        // Sum total allocated to ensure it matches the total payment amount
        $totalAllocated = collect($allocations)->sum('amount');
        if (round($totalAllocated, 2) !== round($data['amount'], 2)) {
            return redirect()->back()->withInput()->with('error', 'Allocated amounts must equal the total payment amount.');
        }

        $payment = $this->paymentService->processPayment($data, $allocations);
        
        return redirect()->route('admin.finance.payments.show', $payment)
                         ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['client', 'paymentMethod', 'bankAccount', 'allocations.invoice', 'receipt']);
        return view('admin.finance.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);
        
        $affectedInvoices = $payment->allocations()->pluck('invoice_id')->unique();
        
        $payment->delete();

        // Recalculate affected invoices using InvoiceService
        $invoiceService = app(\App\Services\Finance\InvoiceService::class);
        foreach ($affectedInvoices as $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoiceService->calculateTotals($invoice);
            }
        }

        return redirect()->route('admin.finance.payments.index')->with('success', 'Payment deleted successfully. Affected invoices recalculated.');
    }
}
