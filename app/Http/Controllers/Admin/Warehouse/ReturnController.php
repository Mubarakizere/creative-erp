<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseReturn;
use App\Services\Warehouse\WarehouseReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected WarehouseReturnService $returnService;

    public function __construct(WarehouseReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseReturn::class);
        $companyId = session('company_id') ?? 1;

        $returns = WarehouseReturn::where('company_id', $companyId)
            ->with(['warehouse', 'inspectedBy'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.returns.index', compact('returns'));
    }

    public function create()
    {
        $this->authorize('create', WarehouseReturn::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();

        return view('admin.warehouse.returns.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WarehouseReturn::class);

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:customer_return,supplier_return,damaged_stock',
            'requires_accounting_adjustment' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        $companyId = session('company_id') ?? 1;

        $warehouseReturn = $this->returnService->logReturn([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'type' => $validated['type'],
            'requires_accounting_adjustment' => $validated['requires_accounting_adjustment'] ?? false,
        ], auth()->id());

        // We explicitly add the items after logReturn since logReturn in the legacy service didn't accept items.
        // But since we just added the JSON column, we can do it directly.
        $warehouseReturn->update([
            'items' => $validated['items']
        ]);

        return redirect()->route('admin.warehouse.returns.show', $warehouseReturn)
            ->with('success', 'Return logged successfully and is pending inspection.');
    }

    public function show(WarehouseReturn $return)
    {
        $this->authorize('view', $return);

        $return->load(['warehouse', 'inspectedBy']);
        
        $bins = WarehouseBin::where('warehouse_id', $return->warehouse_id)->get();
        $products = Product::whereIn('id', collect($return->items)->pluck('product_id'))->get()->keyBy('id');

        return view('admin.warehouse.returns.show', compact('return', 'bins', 'products'));
    }

    public function update(Request $request, WarehouseReturn $return)
    {
        $this->authorize('update', $return);

        $validated = $request->validate([
            'status' => 'required|in:restocked,disposed',
            'notes' => 'nullable|string',
            'loss_amount' => 'nullable|numeric|min:0',
            // If restocked, we need bin assignments for each item
            'restock_items' => 'required_if:status,restocked|array',
            'restock_items.*.product_id' => 'required_with:restock_items|exists:products,id',
            'restock_items.*.quantity' => 'required_with:restock_items|numeric|min:0.01',
            'restock_items.*.bin_id' => 'required_with:restock_items|exists:warehouse_bins,id',
            'restock_items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        $inspectionData = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'loss_amount' => $validated['loss_amount'] ?? 0,
            'items' => $validated['status'] === 'restocked' ? $validated['restock_items'] : [],
        ];

        $this->returnService->inspectReturn($return, $inspectionData, auth()->id());

        $message = $validated['status'] === 'restocked' 
            ? 'Return inspected and items restocked successfully.'
            : 'Return inspected and items disposed. Accounting ledger updated.';

        return redirect()->route('admin.warehouse.returns.show', $return)
            ->with('success', $message);
    }
}
