<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $query = PurchaseRequisition::where('company_id', $companyId)->with(['requestedBy']);
        
        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$request->search}%");
        }

        $requisitions = $query->latest()->paginate(15);
        return view('admin.procurement.requisitions.index', compact('requisitions'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $products = Product::where('company_id', $companyId)->get();
        return view('admin.procurement.requisitions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'code' => 'required|string|unique:purchase_requisitions,code',
            'status' => 'required|in:draft,submitted',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.description' => 'nullable|string',
        ]);

        $pr = PurchaseRequisition::create([
            'company_id' => $companyId,
            'code' => $validated['code'],
            'status' => $validated['status'],
            'requested_by' => auth()->id(),
            'created_by' => auth()->id(),
            'requisition_date' => now(),
        ]);

        foreach ($validated['items'] as $item) {
            $pr->items()->create($item);
        }

        return redirect()->route('admin.procurement.requisitions.index')->with('success', 'Purchase Requisition saved successfully.');
    }

    public function show(PurchaseRequisition $requisition)
    {
        $this->authorize('view', $requisition);
        $requisition->load(['items.product', 'requestedBy']);
        return view('admin.procurement.requisitions.show', compact('requisition'));
    }

    public function approve(PurchaseRequisition $requisition)
    {
        $this->authorize('approve', $requisition);
        
        if ($requisition->requested_by === auth()->id()) {
            return back()->with('error', 'You cannot approve your own requisition.');
        }

        $requisition->update(['status' => 'approved']);
        return back()->with('success', 'Requisition approved successfully.');
    }

    public function compare(PurchaseRequisition $requisition)
    {
        $requisition->load(['quotations.supplier', 'quotations.items.product']);
        return view('admin.procurement.requisitions.compare', compact('requisition'));
    }

    public function acceptQuotation(PurchaseRequisition $requisition, \App\Models\SupplierQuotation $quotation)
    {
        // Approve the quotation and create PO
        $quotation->update(['status' => 'approved']);
        
        $po = \App\Models\PurchaseOrder::create([
            'company_id' => $quotation->company_id,
            'code' => 'PO-' . time(),
            'supplier_id' => $quotation->supplier_id,
            'supplier_quotation_id' => $quotation->id,
            'order_date' => now(),
            'delivery_date' => now()->addDays(7),
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($quotation->items as $item) {
            $po->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'tax' => $item->tax,
                'total' => $item->total,
            ]);
        }

        return redirect()->route('admin.procurement.pos.show', $po->id)->with('success', 'Quotation accepted and PO generated.');
    }
}