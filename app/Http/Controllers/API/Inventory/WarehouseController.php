<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\Inventory\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
        $this->authorizeResource(Warehouse::class, 'warehouse');
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $warehouses = Warehouse::where('company_id', $companyId)
            ->with(['manager', 'zones'])
            ->paginate($request->get('per_page', 15));

        return response()->json($warehouses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_default' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        $warehouse = $this->warehouseService->createWarehouse($validated);

        return response()->json($warehouse, 201);
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        $warehouse->load(['manager', 'zones', 'inventories.product']);
        return response()->json($warehouse);
    }

    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_default' => 'boolean',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $warehouse = $this->warehouseService->updateWarehouse($warehouse, $validated);

        return response()->json($warehouse);
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();
        return response()->json(null, 204);
    }
}
