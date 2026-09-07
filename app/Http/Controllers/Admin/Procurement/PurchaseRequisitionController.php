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

        $query = PurchaseRequisition::where('company_id', $companyId)
            ->with(['requestedBy', 'project', 'department', 'items', 'quotations']);
        
        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stats = [
            'total' => PurchaseRequisition::where('company_id', $companyId)->count(),
            'pending' => PurchaseRequisition::where('company_id', $companyId)->where('status', 'submitted')->count(),
            'approved' => PurchaseRequisition::where('company_id', $companyId)->where('status', 'approved')->count(),
            'draft' => PurchaseRequisition::where('company_id', $companyId)->where('status', 'draft')->count(),
        ];

        $requisitions = $query->latest()->paginate(15);
        return view('admin.procurement.requisitions.index', compact('requisitions', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', PurchaseRequisition::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $products = Product::where('company_id', $companyId)->get();
        $code = app(\App\Services\SequenceService::class)->generate('purchase_requisition', $companyId);
        return view('admin.procurement.requisitions.create', compact('products', 'code'));
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
        $requisition->load(['items.product.unit', 'requestedBy', 'project', 'projectMaterialRequest', 'quotations.supplier', 'department']);
        return view('admin.procurement.requisitions.show', compact('requisition'));
    }

    public function approve(PurchaseRequisition $requisition)
    {
        $this->authorize('approve', $requisition);

        // Allow Super Admin and CEO to approve any requisition
        if ($requisition->requested_by === auth()->id() && !auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('CEO')) {
            return back()->with('error', 'You cannot approve your own requisition.');
        }

        $requisition->update(['status' => 'approved']);

        // Automatically generate RFQs / Supplier Quotations if none exist yet for this requisition
        if (!$requisition->quotations()->exists()) {
            $companyId = $requisition->company_id ?? session('company_id') ?? auth()->user()->company_id ?? 1;
            $suppliers = \App\Models\Supplier::where('company_id', $companyId)->get();

            if ($suppliers->isEmpty()) {
                $supplier = \App\Models\Supplier::firstOrCreate(
                    ['company_id' => $companyId, 'name' => 'General Supplier'],
                    ['code' => 'SUP-001', 'email' => 'supplier@example.com']
                );
                $suppliers = collect([$supplier]);
            }

            foreach ($suppliers as $supplier) {
                try {
                    $code = app(\App\Services\SequenceService::class)->generate('quotation', $companyId);
                } catch (\Throwable $t) {
                    $code = 'RFQ-' . date('Y') . '-' . str_pad($requisition->id . $supplier->id, 6, '0', STR_PAD_LEFT);
                }

                $rfq = \App\Models\SupplierQuotation::create([
                    'company_id' => $companyId,
                    'code' => $code,
                    'supplier_id' => $supplier->id,
                    'purchase_requisition_id' => $requisition->id,
                    'issue_date' => now(),
                    'valid_until' => now()->addDays(14),
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);

                foreach ($requisition->items as $item) {
                    $rfq->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'tax' => 0,
                        'total' => 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.procurement.rfqs.index')->with('success', 'Requisition approved successfully and Request for Quotation (RFQ) generated.');
    }

    public function compare(PurchaseRequisition $requisition)
    {
        $requisition->load([
            'quotations.supplier',
            'quotations.items.product.unit',
            'items.product.unit',
            'requestedBy',
            'project',
            'department'
        ]);
        return view('admin.procurement.requisitions.compare', compact('requisition'));
    }

    public function acceptQuotation(PurchaseRequisition $requisition, \App\Models\SupplierQuotation $quotation)
    {
        // Approve the quotation and create PO
        $quotation->update(['status' => 'approved']);
        
        $po = \App\Models\PurchaseOrder::create([
            'company_id' => $quotation->company_id,
            'code' => app(\App\Services\SequenceService::class)->generate('purchase_order', $quotation->company_id),
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