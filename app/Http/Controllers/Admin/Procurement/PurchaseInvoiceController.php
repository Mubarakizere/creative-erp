<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(PurchaseInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseInvoice::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $query = PurchaseInvoice::where('company_id', $companyId)->with('supplier');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        $invoices = $query->latest()->paginate(15);
        return view('admin.procurement.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', PurchaseInvoice::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        
        $poId = $request->query('purchase_order_id');
        $po = null;
        if ($poId) {
            $po = PurchaseOrder::with(['items.product', 'supplier'])->where('company_id', $companyId)->findOrFail($poId);
        }

        $suppliers = \App\Models\Supplier::where('company_id', $companyId)->get();

        return view('admin.procurement.invoices.create', compact('po', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseInvoice::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
        ]);

        $data = [
            'company_id' => $companyId,
            'invoice_number' => $validated['invoice_number'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_order_id' => $validated['purchase_order_id'] ?? null,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $validated['subtotal'],
            'tax_amount' => $validated['tax_amount'],
            'discount_amount' => $validated['discount_amount'],
            'grand_total' => $validated['grand_total'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
        ];

        $invoice = $this->invoiceService->create($data, $validated['items']);

        if (!empty($validated['purchase_order_id'])) {
            $po = PurchaseOrder::find($validated['purchase_order_id']);
            $po->status = 'completed'; 
            $po->save();
        }

        return redirect()->route('admin.procurement.invoices.show', $invoice->id)->with('success', 'Purchase Invoice created successfully.');
    }

    public function show(PurchaseInvoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['items.product', 'supplier', 'payments']);
        return view('admin.procurement.invoices.show', compact('invoice'));
    }
}
