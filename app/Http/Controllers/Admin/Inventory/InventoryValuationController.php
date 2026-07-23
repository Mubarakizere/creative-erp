<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Inventory\InventoryValuationService;

class InventoryValuationController extends Controller
{
    public function index(InventoryValuationService $valuationService)
    {
        $companyId = session('company_id') ?? 1;

        // Fetch products that track inventory
        $products = Product::where('company_id', $companyId)
            ->where('track_inventory', true)
            ->with(['inventory' => function($query) {
                $query->selectRaw('product_id, sum(available_quantity) as total_available_quantity')
                      ->groupBy('product_id');
            }])
            ->get();

        $valuations = [];
        $totalSystemValue = 0;

        foreach ($products as $product) {
            $availableQty = $product->inventory->sum('total_available_quantity') ?? 0;
            
            if ($availableQty > 0) {
                $value = $valuationService->calculateValuation($product);
                $totalSystemValue += $value;

                $valuations[] = [
                    'product' => $product,
                    'available_quantity' => $availableQty,
                    'valuation_method' => $product->valuation_method ?? 'Standard Cost',
                    'unit_cost' => $product->cost_price,
                    'total_value' => $value,
                ];
            }
        }

        return view('admin.inventory.valuation.index', compact('valuations', 'totalSystemValue'));
    }
}
