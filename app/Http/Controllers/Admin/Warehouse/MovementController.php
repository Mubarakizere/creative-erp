<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseMovement;
use App\Models\WarehouseZone;
use App\Models\InventoryTransaction;
use App\Services\Warehouse\WarehouseMovementService;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    protected WarehouseMovementService $movementService;

    public function __construct(WarehouseMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    public function index()
    {
        $this->authorize('viewAny', WarehouseMovement::class);
        $companyId = session('company_id') ?? 1;

        $items = WarehouseMovement::where('company_id', $companyId)
            ->with(['sourceWarehouse', 'destinationWarehouse', 'product'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.movements.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('create', WarehouseMovement::class);
        $companyId = session('company_id') ?? 1;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $zones = WarehouseZone::whereIn('warehouse_id', $warehouses->pluck('id'))->get();
        $bins = WarehouseBin::whereIn('warehouse_id', $warehouses->pluck('id'))->get();
        $products = Product::where('company_id', $companyId)->get();

        return view('admin.warehouse.movements.create', compact('warehouses', 'zones', 'bins', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WarehouseMovement::class);

        $request->validate([
            'type' => 'required|in:bin_to_bin,zone_to_zone,warehouse_to_warehouse',
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id',
            'source_zone_id' => 'nullable|exists:warehouse_zones,id',
            'destination_zone_id' => 'nullable|exists:warehouse_zones,id',
            'source_bin_id' => 'nullable|exists:warehouse_bins,id',
            'destination_bin_id' => 'nullable|exists:warehouse_bins,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:1000',
        ]);

        $companyId = session('company_id') ?? 1;

        $movement = $this->movementService->requestMovement(
            array_merge($request->all(), ['company_id' => $companyId]),
            auth()->id()
        );

        return redirect()->route('admin.warehouse.movements.show', $movement)
            ->with('success', 'Movement request created successfully.');
    }

    public function show(WarehouseMovement $movement)
    {
        $this->authorize('view', $movement);

        $movement->load(['sourceWarehouse', 'destinationWarehouse', 'sourceZone', 'destinationZone', 'sourceBin', 'destinationBin', 'product', 'approvedBy']);

        // Fetch movement history (InventoryTransactions)
        $history = InventoryTransaction::where('reference_type', WarehouseMovement::class)
            ->where('reference_id', $movement->id)
            ->get();

        return view('admin.warehouse.movements.show', compact('movement', 'history'));
    }

    public function edit(WarehouseMovement $movement)
    {
        $this->authorize('update', $movement);

        return view('admin.warehouse.movements.edit', compact('movement'));
    }

    public function update(Request $request, WarehouseMovement $movement)
    {
        $this->authorize('update', $movement);

        $request->validate([
            'action' => 'required|in:execute,cancel',
        ]);

        if ($request->action === 'execute') {
            $this->movementService->executeMovement($movement, auth()->id());
            $message = 'Movement executed successfully. Inventory has been updated.';
        } else {
            $movement->update(['status' => 'cancelled']);
            $message = 'Movement cancelled.';
        }

        return redirect()->route('admin.warehouse.movements.show', $movement)
            ->with('success', $message);
    }
}
