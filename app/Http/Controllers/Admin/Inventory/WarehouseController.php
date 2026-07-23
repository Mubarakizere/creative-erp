<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Warehouse::class);
        $companyId = session('company_id') ?? 1;
        
        $warehouses = Warehouse::where('company_id', $companyId)
            ->with(['manager', 'zones'])
            ->latest()
            ->paginate(15);
            
        return view('admin.inventory.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $this->authorize('create', Warehouse::class);
        $companyId = session('company_id') ?? 1;
        
        // For now, any user can be a manager. Can be filtered by role if needed later.
        $managers = User::where('company_id', $companyId)->orWhereNull('company_id')->get();
        
        return view('admin.inventory.warehouses.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Warehouse::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_default' => 'boolean',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_default'] = $request->boolean('is_default');

        DB::transaction(function () use ($validated, $companyId) {
            if ($validated['is_default']) {
                Warehouse::where('company_id', $companyId)->update(['is_default' => false]);
            }
            Warehouse::create($validated);
        });

        return redirect()->route('admin.inventory.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        $companyId = session('company_id') ?? 1;
        $managers = User::where('company_id', $companyId)->orWhereNull('company_id')->get();
        
        $warehouse->load('zones');
        
        return view('admin.inventory.warehouses.edit', compact('warehouse', 'managers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorize('update', $warehouse);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_default' => 'boolean',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['is_default'] = $request->boolean('is_default');

        DB::transaction(function () use ($validated, $warehouse, $companyId) {
            if ($validated['is_default'] && !$warehouse->is_default) {
                Warehouse::where('company_id', $companyId)
                    ->where('id', '!=', $warehouse->id)
                    ->update(['is_default' => false]);
            }
            $warehouse->update($validated);
        });

        return redirect()->route('admin.inventory.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('delete', $warehouse);
        
        if ($warehouse->is_default) {
            return back()->with('error', 'Cannot delete the default warehouse.');
        }

        if ($warehouse->inventories()->count() > 0) {
            return back()->with('error', 'Cannot delete warehouse with associated inventory.');
        }

        $warehouse->delete();
        
        return redirect()->route('admin.inventory.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
