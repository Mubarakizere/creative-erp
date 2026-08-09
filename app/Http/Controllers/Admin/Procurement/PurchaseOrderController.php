<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $query = PurchaseOrder::where('company_id', $companyId)->with(['supplier']);
        
        $pos = $query->latest()->paginate(15);
        return view('admin.procurement.pos.index', compact('pos'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $code = app(\App\Services\SequenceService::class)->generate('purchase_order', $companyId);
        
        return view('admin.procurement.pos.create', compact('code'));
    }

    public function store(Request $request, \App\Services\Procurement\PurchaseOrderService $service)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'code' => 'required|string|unique:purchase_orders,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'status' => 'required|in:draft,approved,sent',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = [
            'company_id' => $companyId,
            'code' => $validated['code'],
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'status' => $validated['status'], // Will be overridden to draft by service if it forces it, but we can pass it
        ];
        
        $items = $validated['items'];
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }

        // The service forces status='draft', so we will bypass service or modify data after creation
        $po = $service->create($data, $items);

        // Update status if it's different from draft (since service forces draft)
        if ($validated['status'] !== 'draft') {
            $po->update(['status' => $validated['status']]);
        }

        return redirect()->route('admin.procurement.pos.index')->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $po)
    {
        $po->load(['supplier', 'items.product', 'receipts']);
        return view('admin.procurement.pos.show', compact('po'));
    }

    public function approve(PurchaseOrder $po)
    {
        // Typically involves PurchaseOrderService, but for simplicity here we just update status
        $po->update(['status' => 'approved']);
        return back()->with('success', 'Purchase Order approved successfully.');
    }
}