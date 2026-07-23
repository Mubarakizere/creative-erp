<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    protected GoodsReceiptService $service;

    public function __construct(GoodsReceiptService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create', GoodsReceipt::class);
        $data = $request->validate([
            'code' => 'required|string|unique:goods_receipts,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
        ]);
        $items = $data['items'];
        unset($data['items']);
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $this->authorize('view', $goodsReceipt);
        $goodsReceipt->load(['items.product', 'supplier', 'purchaseOrder']);
        return response()->json($goodsReceipt);
    }
}