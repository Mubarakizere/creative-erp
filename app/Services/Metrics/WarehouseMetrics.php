<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Services\Metrics\Traits\FiltersMetrics;
use App\Models\Warehouse;
use App\Models\InventoryTransfer;
use App\Models\InventoryAdjustment;

class WarehouseMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        $totalWarehouses = Warehouse::count(); // Usually warehouse doesn't have company_id directly, but if we need, we should filter.
        // Wait, warehouse migration didn't have company_id? All my migrations have company_id through the scaffold replacement script!
        
        return [
            'total_warehouses' => Warehouse::where(function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->count(),
            
            'pending_transfers' => InventoryTransfer::whereHas('fromWarehouse', function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->where('status', 'pending')->count(),
            
            'pending_adjustments' => InventoryAdjustment::whereHas('warehouse', function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->where('status', 'pending')->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        return [
            'pendingAdjustmentsList' => InventoryAdjustment::whereHas('warehouse', function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->with(['warehouse'])->where('status', 'pending')->latest()->take(5)->get(),
            
            'pendingTransfersList' => InventoryTransfer::whereHas('fromWarehouse', function($q) use ($companyId) {
                if ($companyId) $q->where('company_id', $companyId);
            })->with(['fromWarehouse', 'toWarehouse'])->where('status', 'pending')->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
