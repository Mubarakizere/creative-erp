<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use Illuminate\Http\Request;

class WarehouseZoneController extends Controller
{
    public function store(Request $request, Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse); // Same permission as editing warehouse
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['company_id'] = $warehouse->company_id;
        $validated['warehouse_id'] = $warehouse->id;

        WarehouseZone::create($validated);

        return redirect()->route('admin.inventory.warehouses.edit', $warehouse)
            ->with('success', 'Zone added successfully.');
    }

    public function update(Request $request, Warehouse $warehouse, WarehouseZone $zone)
    {
        $this->authorize('update', $warehouse);
        
        if ($zone->warehouse_id !== $warehouse->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $zone->update($validated);

        return redirect()->route('admin.inventory.warehouses.edit', $warehouse)
            ->with('success', 'Zone updated successfully.');
    }

    public function destroy(Warehouse $warehouse, WarehouseZone $zone)
    {
        $this->authorize('update', $warehouse);

        if ($zone->warehouse_id !== $warehouse->id) {
            abort(404);
        }

        $zone->delete();

        return redirect()->route('admin.inventory.warehouses.edit', $warehouse)
            ->with('success', 'Zone deleted successfully.');
    }
}
