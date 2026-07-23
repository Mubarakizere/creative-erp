<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\Procurement\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $service;

    public function __construct(PurchaseOrderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PurchaseOrder::class);
        return response()->json($this->service->list($request->all()));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseOrder::class);
        $data = $request->validate([
            'code' => 'required|string|unique:purchase_orders,code',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        $items = $data['items'];
        unset($data['items']);
        // calculate item total
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }
        $data['subtotal'] = collect($items)->sum('total');
        $data['grand_total'] = $data['subtotal'];

        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view', $purchaseOrder);
        $purchaseOrder->load(['items.product', 'supplier', 'quotation']);
        return response()->json($purchaseOrder);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);
        return response()->json($this->service->approve($purchaseOrder));
    }
}