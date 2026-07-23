<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Services\Metrics\Traits\FiltersMetrics;
use App\Models\Inventory;
use App\Models\InventoryValuation;
use App\Models\Product;
use App\Models\InventoryTransaction;

class InventoryMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        return [
            'total_inventory_value' => InventoryValuation::whereHas('product', function ($query) use ($companyId) {
                if ($companyId) {
                    $query->where('company_id', $companyId);
                }
            })->sum('total_value'),
            
            'low_stock_products' => Product::where(function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->whereHas('inventory', function ($query) {
                $query->whereColumn('available_quantity', '<=', 'products.minimum_stock');
            })->count(),

            'out_of_stock_products' => Product::where(function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->whereDoesntHave('inventory', function ($query) {
                $query->where('available_quantity', '>', 0);
            })->count(),
            
            'total_products' => Product::where(function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        return [
            'recentInventoryTransactions' => InventoryTransaction::whereHas('inventory.product', function ($query) use ($companyId) {
                if ($companyId) {
                    $query->where('company_id', $companyId);
                }
            })->with(['inventory.product', 'user'])->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
