<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use App\Models\InventoryAdjustment;
use App\Services\Inventory\InventoryValuationService;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    public function index(InventoryValuationService $valuationService)
    {
        $companyId = session('company_id') ?? 1;

        // 1. Inventory Value & Product Count
        $products = Product::where('company_id', $companyId)
            ->where('track_inventory', true)
            ->with(['inventory' => function($query) {
                $query->selectRaw('product_id, sum(available_quantity) as total_qty')->groupBy('product_id');
            }])
            ->get();

        $productCount = $products->count();
        $inventoryValue = 0;
        
        $lowStock = collect();
        $outOfStock = collect();
        $overstock = collect();

        foreach ($products as $product) {
            $totalQty = $product->inventory->sum('total_qty') ?? 0;
            
            if ($totalQty > 0) {
                $inventoryValue += $valuationService->calculateValuation($product);
            }

            // 2. Stock Alerts
            if ($totalQty <= 0) {
                $outOfStock->push(['product' => $product, 'qty' => $totalQty]);
            } elseif ($totalQty <= $product->minimum_stock) {
                $lowStock->push(['product' => $product, 'qty' => $totalQty]);
            } elseif ($product->maximum_stock > 0 && $totalQty > $product->maximum_stock) {
                $overstock->push(['product' => $product, 'qty' => $totalQty]);
            }
        }

        // 3. Warehouse Utilization
        $warehouses = Warehouse::where('company_id', $companyId)
            ->with(['inventories' => function($query) {
                $query->selectRaw('warehouse_id, count(distinct product_id) as unique_products, sum(available_quantity) as total_items')
                      ->having('total_items', '>', 0)
                      ->groupBy('warehouse_id');
            }])
            ->get()
            ->map(function ($warehouse) {
                $inventoryData = $warehouse->inventories->first();
                return [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'unique_products' => $inventoryData->unique_products ?? 0,
                    'total_items' => $inventoryData->total_items ?? 0,
                ];
            });

        // 4. Recent Transactions
        $recentTransactions = InventoryTransaction::where('company_id', $companyId)
            ->with(['inventory.product', 'inventory.warehouse'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Pending Adjustments
        $pendingAdjustments = InventoryAdjustment::where('company_id', $companyId)
            ->where('status', 'pending')
            ->with(['warehouse'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.inventory.dashboard.index', compact(
            'inventoryValue',
            'productCount',
            'lowStock',
            'outOfStock',
            'overstock',
            'warehouses',
            'recentTransactions',
            'pendingAdjustments'
        ));
    }
}
