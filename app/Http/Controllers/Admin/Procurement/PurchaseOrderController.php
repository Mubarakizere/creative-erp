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
        $query = PurchaseOrder::where('company_id', $companyId)->with(['supplier', 'items']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pos = $query->latest()->paginate(15)->withQueryString();

        $allPos = PurchaseOrder::where('company_id', $companyId)->with('items')->get();
        $stats = [
            'total' => $allPos->count(),
            'draft' => $allPos->where('status', 'draft')->count(),
            'approved' => $allPos->whereIn('status', ['approved', 'sent'])->count(),
            'received' => $allPos->whereIn('status', ['partially_received', 'received'])->count(),
            'total_value' => $allPos->sum(function($po) {
                return $po->grand_total > 0 ? $po->grand_total : $po->items->sum('total');
            }),
        ];

        return view('admin.procurement.pos.index', compact('pos', 'stats'));
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
            'status' => $validated['status'],
        ];
        
        $items = $validated['items'];
        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
        }

        $po = $service->create($data, $items);

        if ($validated['status'] !== 'draft') {
            $po->update(['status' => $validated['status']]);
        }

        return redirect()->route('admin.procurement.pos.index')->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $po)
    {
        $po->load(['supplier', 'items.product', 'goodsReceipts', 'receipts']);
        return view('admin.procurement.pos.show', compact('po'));
    }

    public function pdf(PurchaseOrder $po)
    {
        $this->authorize('view', $po);
        $po->load(['supplier', 'items.product', 'project', 'company']);
        return app(\App\Services\RecordPdfService::class)->download('Purchase Order', $po->code, [
            'Supplier' => $po->supplier?->name, 'Status' => str_replace('_', ' ', ucfirst($po->status)),
            'Order date' => $po->order_date?->format('d M Y'), 'Delivery date' => $po->delivery_date?->format('d M Y'),
            'Project' => $po->project?->name, 'Notes' => $po->notes,
        ], [ ['label' => 'Item', 'key' => 'name'], ['label' => 'Quantity', 'key' => 'quantity'], ['label' => 'Unit price', 'key' => 'unit_price'], ['label' => 'Total', 'key' => 'total'] ],
            $po->items->map(fn($item) => ['name' => $item->product?->name, 'quantity' => $item->quantity, 'unit_price' => number_format($item->unit_price, 2), 'total' => number_format($item->total, 2)])->all(),
            ['Subtotal' => number_format($po->subtotal, 2), 'Tax' => number_format($po->tax_total, 2), 'Discount' => number_format($po->discount_total, 2), 'Total' => number_format($po->grand_total, 2)], $po->company?->name);
    }

    public function approve(PurchaseOrder $po)
    {
        $po->update(['status' => 'approved']);
        return back()->with('success', 'Purchase Order approved successfully.');
    }

    public function destroy(PurchaseOrder $po)
    {
        $po->delete();
        return redirect()->route('admin.procurement.pos.index')->with('success', 'Purchase Order deleted successfully.');
    }
}
