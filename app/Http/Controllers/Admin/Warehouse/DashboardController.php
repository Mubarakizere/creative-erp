<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehouseBin;
use App\Models\WarehousePicking;
use App\Models\WarehousePacking;
use App\Models\WarehouseShipment;
use App\Models\WarehouseReturn;
use App\Models\WarehouseTask;
use App\Models\WarehouseCycleCount;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? auth()->user()->company_id ?? 1;

        // Warehouse Utilization & Bin Capacity
        // Compute total capacity vs current quantity for all bins in this company
        $bins = WarehouseBin::where('company_id', $companyId)->get();
        
        $totalCapacity = $bins->sum('capacity');
        $totalQuantity = $bins->sum('current_quantity');
        
        $warehouseUtilization = $totalCapacity > 0 ? min(100, round(($totalQuantity / $totalCapacity) * 100, 2)) : 0;
        
        $binCapacity = [
            'full' => 0,
            'partial' => 0,
            'empty' => 0,
            'total' => $bins->count(),
        ];
        
        foreach ($bins as $bin) {
            if ($bin->status === 'full' || ($bin->capacity > 0 && $bin->current_quantity >= $bin->capacity)) {
                $binCapacity['full']++;
            } elseif ($bin->current_quantity > 0) {
                $binCapacity['partial']++;
            } else {
                $binCapacity['empty']++;
            }
        }

        // Pending Operations
        $pendingPicks = WarehousePicking::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'picking'])
            ->count();
            
        $pendingPacking = WarehousePacking::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'packing'])
            ->count();
            
        $pendingShipments = WarehouseShipment::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'ready'])
            ->count();
            
        $pendingReturns = WarehouseReturn::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'inspecting'])
            ->count();
            
        $warehouseTasks = WarehouseTask::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
            
        $cycleCountProgress = WarehouseCycleCount::where('company_id', $companyId)
            ->whereIn('status', ['in_progress', 'variance_detected'])
            ->count();

        $metrics = [
            'utilization' => $warehouseUtilization,
            'bin_capacity' => $binCapacity,
            'pending_picks' => $pendingPicks,
            'pending_packing' => $pendingPacking,
            'pending_shipments' => $pendingShipments,
            'pending_returns' => $pendingReturns,
            'warehouse_tasks' => $warehouseTasks,
            'cycle_counts' => $cycleCountProgress,
        ];

        return view('admin.warehouse.dashboard.index', compact('metrics'));
    }
}
