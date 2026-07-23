<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $query = GoodsReceipt::where('company_id', $companyId)->with(['purchaseOrder.supplier']);
        
        $receipts = $query->latest()->paginate(15);
        return view('admin.procurement.receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $poId = $request->input('po_id');
        $po = PurchaseOrder::with('items.product')->findOrFail($poId);
        
        // Load warehouses for receiving
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $warehouses = \App\Models\Warehouse::where('company_id', $companyId)->get();

        return view('admin.procurement.receipts.create', compact('po', 'warehouses'));
    }

    public function store(Request $request, GoodsReceiptService $service)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'delivery_note_number' => 'nullable|string',
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
        ]);

        $po = PurchaseOrder::findOrFail($validated['purchase_order_id']);

        $data = [
            'company_id' => $companyId,
            'purchase_order_id' => $po->id,
            'supplier_id' => $po->supplier_id,
            'warehouse_id' => $validated['warehouse_id'],
            'receipt_date' => $validated['receipt_date'],
            'delivery_note_number' => $validated['delivery_note_number'] ?? 'DN-' . time(),
            'code' => 'GR-' . time(),
            'status' => 'completed',
            'created_by' => auth()->id(),
        ];

        $itemsToService = [];
        foreach ($validated['items'] as $item) {
            $poItem = \App\Models\PurchaseOrderItem::find($item['purchase_order_item_id']);
            $itemsToService[] = [
                'purchase_order_item_id' => $item['purchase_order_item_id'],
                'product_id' => $poItem->product_id,
                'quantity_received' => $item['received_quantity'],
                'quantity_rejected' => $item['rejected_quantity'] ?? 0,
            ];
        }

        $receipt = $service->create($data, $itemsToService);

        return redirect()->route('admin.procurement.receipts.index')->with('success', 'Goods Receipt created and inventory updated.');
    }

    public function show(GoodsReceipt $receipt)
    {
        $receipt->load(['purchaseOrder', 'items.purchaseOrderItem.product', 'warehouse']);
        return view('admin.procurement.receipts.show', compact('receipt'));
    }
}