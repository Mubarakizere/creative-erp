<?php
namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', GoodsReceipt::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $query = GoodsReceipt::where('company_id', $companyId)->with(['purchaseOrder.supplier']);
        
        $receipts = $query->latest()->paginate(15);
        return view('admin.procurement.receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', GoodsReceipt::class);
        
        $poId = $request->input('po_id');
        $po = PurchaseOrder::with('items.product')->findOrFail($poId);
        
        Gate::authorize('view', $po);

        if (!in_array($po->status, ['approved', 'partially_received'])) {
            return redirect()->route('admin.procurement.pos.index')->with('error', 'Purchase order is not eligible for receiving.');
        }
        
        // Load warehouses for receiving
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;
        $warehouses = \App\Models\Warehouse::where('company_id', $companyId)->get();
        
        $code = app(\App\Services\SequenceService::class)->generate('goods_receipt', $companyId);

        return view('admin.procurement.receipts.create', compact('po', 'warehouses', 'code'));
    }

    public function store(Request $request, GoodsReceiptService $service)
    {
        Gate::authorize('create', GoodsReceipt::class);
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'code' => 'required|string|unique:goods_receipts,code',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => [
                'required',
                Rule::exists('warehouses', 'id')->where('company_id', $companyId),
            ],
            'receipt_date' => 'required|date',
            'delivery_note_number' => 'nullable|string',
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
        ]);

        $po = PurchaseOrder::with('items')->findOrFail($validated['purchase_order_id']);
        Gate::authorize('view', $po);

        if (!in_array($po->status, ['approved', 'partially_received'])) {
            throw ValidationException::withMessages(['purchase_order_id' => 'Purchase order is not eligible for receiving.']);
        }

        $itemsToService = [];
        foreach ($validated['items'] as $item) {
            $poItem = $po->items->firstWhere('id', $item['purchase_order_item_id']);
            
            if (!$poItem) {
                continue; // Skip invalid items, although validated above
            }
            
            $remaining = max(0, $poItem->quantity - $poItem->received_quantity);
            if ($item['received_quantity'] > $remaining) {
                throw ValidationException::withMessages([
                    'items' => "Cannot receive more than remaining quantity for product {$poItem->product?->name}."
                ]);
            }
            
            if ($item['received_quantity'] > 0 || ($item['rejected_quantity'] ?? 0) > 0) {
                $itemsToService[] = [
                    'purchase_order_item_id' => $item['purchase_order_item_id'],
                    'product_id' => $poItem->product_id,
                    'quantity_received' => $item['received_quantity'],
                    'quantity_rejected' => $item['rejected_quantity'] ?? 0,
                ];
            }
        }

        if (empty($itemsToService)) {
            throw ValidationException::withMessages(['items' => 'No items selected for receiving.']);
        }

        $data = [
            'company_id' => $companyId,
            'purchase_order_id' => $po->id,
            'supplier_id' => $po->supplier_id,
            'warehouse_id' => $validated['warehouse_id'],
            'receipt_date' => $validated['receipt_date'],
            'delivery_note_number' => $validated['delivery_note_number'] ?? 'DN-' . time(),
            'code' => $validated['code'],
            'status' => 'completed',
            'created_by' => auth()->id(),
        ];

        $receipt = $service->create($data, $itemsToService);

        return redirect()->route('admin.procurement.receipts.index')->with('success', 'Goods Receipt created and inventory updated.');
    }

    public function show(GoodsReceipt $receipt)
    {
        Gate::authorize('view', $receipt);
        $receipt->load(['purchaseOrder', 'items.purchaseOrderItem.product', 'warehouse']);
        return view('admin.procurement.receipts.show', compact('receipt'));
    }
}