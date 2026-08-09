<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\WarehouseCycleCount;
use App\Services\Warehouse\CycleCountService;
use Illuminate\Http\Request;

class CycleCountController extends Controller
{
    protected CycleCountService $cycleCountService;

    public function __construct(CycleCountService $cycleCountService)
    {
        $this->cycleCountService = $cycleCountService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseCycleCount::class);
        $companyId = session('company_id') ?? 1;

        $cycleCounts = WarehouseCycleCount::where('company_id', $companyId)
            ->with(['warehouse', 'assignedTo', 'approvedBy'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.cycle-counts.index', compact('cycleCounts'));
    }

    public function create()
    {
        $this->authorize('create', WarehouseCycleCount::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        // Just fetch some inventory options for simplicity in the UI
        $inventories = Inventory::where('company_id', $companyId)->with(['product', 'warehouseBin'])->get();
        $cycle_count_number = app(\App\Services\SequenceService::class)->generate('cycle_count', $companyId);

        return view('admin.warehouse.cycle-counts.create', compact('warehouses', 'inventories', 'cycle_count_number'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WarehouseCycleCount::class);

        $request->validate([
            'cycle_count_number' => 'required|string|unique:warehouse_cycle_counts,cycle_count_number',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:daily,weekly,monthly,abc,manual',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $companyId = session('company_id') ?? 1;

        $cycleCount = $this->cycleCountService->initiateCount(
            array_merge($request->all(), ['company_id' => $companyId]),
            auth()->id()
        );

        return redirect()->route('admin.warehouse.cycle-counts.show', $cycleCount)
            ->with('success', 'Cycle count initiated successfully.');
    }

    public function show(WarehouseCycleCount $cycleCount)
    {
        $this->authorize('view', $cycleCount);

        $cycleCount->load(['warehouse', 'stockCount.items.inventory.product', 'stockCount.items.inventory.warehouseBin', 'assignedTo', 'approvedBy']);

        $inventories = Inventory::where('warehouse_id', $cycleCount->warehouse_id)
            ->with(['product', 'warehouseBin'])
            ->get();

        return view('admin.warehouse.cycle-counts.show', compact('cycleCount', 'inventories'));
    }

    public function update(Request $request, WarehouseCycleCount $cycleCount)
    {
        $this->authorize('update', $cycleCount);

        $request->validate([
            'action' => 'required|in:record,approve',
            // If action is record
            'items' => 'required_if:action,record|array',
            'items.*.inventory_id' => 'required_with:items|exists:inventories,id',
            'items.*.counted_quantity' => 'required_with:items|numeric|min:0',
        ]);

        if ($request->action === 'record') {
            $this->cycleCountService->recordCount($cycleCount, $request->items, auth()->id());
            $message = 'Count recorded successfully. ' . ($cycleCount->fresh()->status === 'variance_detected' ? 'Variances require approval.' : 'No variances detected.');
        } else {
            // Action is approve
            $this->cycleCountService->approveVariance($cycleCount, auth()->id());
            $message = 'Variances approved and inventory adjusted successfully.';
        }

        return redirect()->route('admin.warehouse.cycle-counts.show', $cycleCount)
            ->with('success', $message);
    }
}
