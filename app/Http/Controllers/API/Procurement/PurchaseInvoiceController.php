<?php
namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    protected PurchaseInvoiceService $service;

    public function __construct(PurchaseInvoiceService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->authorize('create', PurchaseInvoice::class);
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_invoices,invoice_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        $items = $data['items'];
        unset($data['items']);
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }
        $data['subtotal'] = collect($items)->sum('total');
        $data['grand_total'] = $data['subtotal'];
        
        return response()->json($this->service->create($data, $items), 201);
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $this->authorize('view', $purchaseInvoice);
        $purchaseInvoice->load(['items.product', 'supplier']);
        return response()->json($purchaseInvoice);
    }
}