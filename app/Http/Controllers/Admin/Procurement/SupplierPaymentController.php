<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\PurchaseInvoice;
use App\Services\Procurement\SupplierPaymentService;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(SupplierPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', SupplierPayment::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $query = SupplierPayment::where('company_id', $companyId)->with(['supplier', 'invoice']);

        if ($request->filled('search')) {
            $query->where('payment_number', 'like', "%{$request->search}%");
        }

        $payments = $query->latest()->paginate(15);
        return view('admin.procurement.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', SupplierPayment::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        
        $invoiceId = $request->query('purchase_invoice_id');
        $invoice = null;
        if ($invoiceId) {
            $invoice = PurchaseInvoice::with('supplier')->where('company_id', $companyId)->findOrFail($invoiceId);
        }

        $suppliers = \App\Models\Supplier::where('company_id', $companyId)->get();

        return view('admin.procurement.payments.create', compact('invoice', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SupplierPayment::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'payment_number' => 'required|string|unique:supplier_payments,payment_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'company_id' => $companyId,
            'payment_number' => $validated['payment_number'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_invoice_id' => $validated['purchase_invoice_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        $payment = $this->paymentService->create($data);

        return redirect()->route('admin.procurement.payments.show', $payment->id)->with('success', 'Supplier Payment recorded successfully.');
    }

    public function show(SupplierPayment $payment)
    {
        $this->authorize('view', $payment);
        $payment->load(['supplier', 'invoice']);
        return view('admin.procurement.payments.show', compact('payment'));
    }
}
