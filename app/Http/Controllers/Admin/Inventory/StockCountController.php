<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\Inventory\InventoryEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockCountController extends Controller
{
    public function index()
    {
        $companyId = session('company_id') ?? 1;

        $counts = StockCount::where('company_id', $companyId)
            ->with(['warehouse', 'createdByUser', 'approvedBy'])
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin.inventory.stock-counts.index', compact('counts'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        return view('admin.inventory.stock-counts.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:manual,cycle',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $stockCount = DB::transaction(function () use ($validated, $companyId) {
            $count = StockCount::create([
                'company_id' => $companyId,
                'warehouse_id' => $validated['warehouse_id'],
                'type' => $validated['type'],
                'status' => 'in_progress',
                'created_by' => auth()->id(),
            ]);

            // Snapshot system quantities for selected products
            foreach ($validated['product_ids'] as $productId) {
                $inventory = Inventory::where('product_id', $productId)
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                $count->items()->create([
                    'product_id' => $productId,
                    'system_quantity' => $inventory ? $inventory->available_quantity + $inventory->reserved_quantity : 0,
                    'counted_quantity' => null,
                    'variance' => 0,
                ]);
            }

            return $count;
        });

        return redirect()->route('admin.inventory.stock-counts.show', $stockCount)
            ->with('success', 'Stock count created. Enter your counted quantities below.');
    }

    public function show(StockCount $stockCount)
    {
        $stockCount->load(['warehouse', 'items.product', 'approvedBy', 'createdByUser']);

        return view('admin.inventory.stock-counts.show', compact('stockCount'));
    }

    public function update(Request $request, StockCount $stockCount)
    {
        if ($stockCount->status === 'approved') {
            return back()->with('error', 'This count has already been approved.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_count_items,id',
            'items.*.counted_quantity' => 'required|numeric|min:0',
        ]);

        $varianceDetected = false;

        DB::transaction(function () use ($validated, $stockCount, &$varianceDetected) {
            foreach ($validated['items'] as $itemData) {
                $item = StockCountItem::findOrFail($itemData['id']);
                $variance = $itemData['counted_quantity'] - $item->system_quantity;

                $item->update([
                    'counted_quantity' => $itemData['counted_quantity'],
                    'variance' => $variance,
                ]);

                if ($variance != 0) {
                    $varianceDetected = true;
                }
            }

            $stockCount->update([
                'status' => 'counted',
                'variance_detected' => $varianceDetected,
            ]);
        });

        return redirect()->route('admin.inventory.stock-counts.show', $stockCount)
            ->with('success', 'Quantities saved. ' . ($varianceDetected ? 'Variances detected!' : 'No variances found.'));
    }

    public function approve(StockCount $stockCount, InventoryEngine $engine)
    {
        if ($stockCount->status === 'approved') {
            return back()->with('error', 'This count has already been approved.');
        }

        if ($stockCount->status !== 'counted') {
            return back()->with('error', 'Please enter counted quantities before approving.');
        }

        DB::transaction(function () use ($stockCount, $engine) {
            $stockCount->load('items.product', 'warehouse');

            foreach ($stockCount->items as $item) {
                if ($item->variance == 0) continue;

                $product = $item->product;
                $warehouse = $stockCount->warehouse;

                if ($item->variance > 0) {
                    // Counted MORE than system — stock in
                    $engine->stockIn(
                        $product,
                        $warehouse,
                        abs($item->variance),
                        'count_adjustment',
                        $stockCount,
                        null, null,
                        auth()->id()
                    );
                } else {
                    // Counted LESS than system — stock out
                    $engine->stockOut(
                        $product,
                        $warehouse,
                        abs($item->variance),
                        'count_adjustment',
                        $stockCount,
                        null, null,
                        auth()->id()
                    );
                }
            }

            $stockCount->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.inventory.stock-counts.show', $stockCount)
            ->with('success', 'Stock count approved. Adjustment transactions have been generated for all variances.');
    }
}
