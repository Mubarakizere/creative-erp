<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseBinController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', \App\Models\WarehouseBin::class);
        $companyId = session('company_id') ?? 1;

        $bins = \App\Models\WarehouseBin::where('company_id', $companyId)
            ->with(['zone.warehouse'])
            ->latest()
            ->paginate(15);

        return view('admin.warehouse.bins.index', compact('bins'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\WarehouseBin::class);
        $companyId = session('company_id') ?? 1;

        $zones = \App\Models\WarehouseZone::where('company_id', $companyId)
            ->with('warehouse')
            ->get();

        return view('admin.warehouse.bins.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\WarehouseBin::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'warehouse_zone_id' => 'required|exists:warehouse_zones,id',
            'code' => 'required|string|max:255',
            'aisle' => 'nullable|string|max:255',
            'rack' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,full,maintenance',
        ]);

        $validated['company_id'] = $companyId;
        $validated['current_quantity'] = 0; // Default when created

        // Convert product types from string to JSON array if needed, but keeping simple for now
        $validated['allowed_product_types'] = json_encode([]); 

        \App\Models\WarehouseBin::create($validated);

        return redirect()->route('admin.warehouse.bins.index')
            ->with('success', 'Bin created successfully.');
    }

    public function show(\App\Models\WarehouseBin $bin)
    {
        $this->authorize('view', $bin);
        $bin->load('zone.warehouse');
        return view('admin.warehouse.bins.show', compact('bin'));
    }

    public function edit(\App\Models\WarehouseBin $bin)
    {
        $this->authorize('update', $bin);
        $companyId = session('company_id') ?? 1;

        $zones = \App\Models\WarehouseZone::where('company_id', $companyId)
            ->with('warehouse')
            ->get();

        return view('admin.warehouse.bins.edit', compact('bin', 'zones'));
    }

    public function update(Request $request, \App\Models\WarehouseBin $bin)
    {
        $this->authorize('update', $bin);

        $validated = $request->validate([
            'warehouse_zone_id' => 'required|exists:warehouse_zones,id',
            'code' => 'required|string|max:255',
            'aisle' => 'nullable|string|max:255',
            'rack' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,full,maintenance',
        ]);

        $bin->update($validated);

        return redirect()->route('admin.warehouse.bins.index')
            ->with('success', 'Bin updated successfully.');
    }

    public function destroy(\App\Models\WarehouseBin $bin)
    {
        $this->authorize('delete', $bin);
        $bin->delete();

        return redirect()->route('admin.warehouse.bins.index')
            ->with('success', 'Bin deleted successfully.');
    }
}
