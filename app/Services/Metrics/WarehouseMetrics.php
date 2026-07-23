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
        
        return [
            'total_warehouses' => Warehouse::where('company_id', $companyId)->count(),
            'pending_picks' => \App\Models\WarehousePicking::where('company_id', $companyId)->where('status', 'pending')->count(),
            'pending_packing' => \App\Models\WarehousePacking::where('company_id', $companyId)->where('status', 'pending')->count(),
            'pending_shipments' => \App\Models\WarehouseShipment::where('company_id', $companyId)->where('status', 'pending')->count(),
            'pending_returns' => \App\Models\WarehouseReturn::where('company_id', $companyId)->where('status', 'pending')->count(),
            'active_warehouse_tasks' => \App\Models\WarehouseTask::where('company_id', $companyId)->whereIn('status', ['pending', 'in_progress'])->count(),
            'warehouse_utilization' => $this->calculateUtilization($companyId),
            'inventory_accuracy' => $this->calculateInventoryAccuracy($companyId),
            'average_pick_time' => $this->calculateAveragePickTime($companyId),
            'average_put_away_time' => $this->calculateAveragePutAwayTime($companyId),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id;
        
        return [
            'pendingPicksList' => \App\Models\WarehousePicking::where('company_id', $companyId)->where('status', 'pending')->latest()->take(5)->get(),
            'pendingShipmentsList' => \App\Models\WarehouseShipment::where('company_id', $companyId)->where('status', 'pending')->latest()->take(5)->get(),
            'cycleCountProgress' => \App\Models\WarehouseCycleCount::where('company_id', $companyId)->whereIn('status', ['pending', 'in_progress', 'variance_detected'])->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
    
    private function calculateUtilization($companyId)
    {
        $totalCapacity = \App\Models\WarehouseBin::where('company_id', $companyId)->sum('capacity');
        if ($totalCapacity == 0) return 0;
        
        $totalQuantity = \App\Models\WarehouseBin::where('company_id', $companyId)->sum('current_quantity');
        return round(($totalQuantity / $totalCapacity) * 100, 2);
    }
    
    private function calculateInventoryAccuracy($companyId)
    {
        $totalCounts = \App\Models\StockCountItem::whereHas('stockCount', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->count();
        
        if ($totalCounts == 0) return 100; // 100% accurate if no counts yet
        
        $varianceCounts = \App\Models\StockCountItem::whereHas('stockCount', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->where('variance', '!=', 0)->count();
        
        return round((($totalCounts - $varianceCounts) / $totalCounts) * 100, 2);
    }
    
    private function calculateAveragePickTime($companyId)
    {
        $picks = \App\Models\WarehouseTask::where('company_id', $companyId)
            ->where('type', 'picking')
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();
            
        if ($picks->isEmpty()) return 0;
        
        $totalMinutes = 0;
        foreach ($picks as $pick) {
            $totalMinutes += \Carbon\Carbon::parse($pick->started_at)->diffInMinutes(\Carbon\Carbon::parse($pick->completed_at));
        }
        
        return round($totalMinutes / $picks->count(), 2);
    }
    
    private function calculateAveragePutAwayTime($companyId)
    {
        $tasks = \App\Models\WarehouseTask::where('company_id', $companyId)
            ->where('type', 'put_away')
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();
            
        if ($tasks->isEmpty()) return 0;
        
        $totalMinutes = 0;
        foreach ($tasks as $task) {
            $totalMinutes += \Carbon\Carbon::parse($task->started_at)->diffInMinutes(\Carbon\Carbon::parse($task->completed_at));
        }
        
        return round($totalMinutes / $tasks->count(), 2);
    }
}
