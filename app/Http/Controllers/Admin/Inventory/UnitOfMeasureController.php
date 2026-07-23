<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;

class UnitOfMeasureController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', UnitOfMeasure::class);
        $companyId = session('company_id') ?? 1;
        $uoms = UnitOfMeasure::where('company_id', $companyId)->latest()->paginate(15);
        
        return view('admin.inventory.units.index', compact('uoms'));
    }

    public function create()
    {
        $this->authorize('create', UnitOfMeasure::class);
        return view('admin.inventory.units.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', UnitOfMeasure::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);
        
        $validated['company_id'] = session('company_id') ?? 1;
        UnitOfMeasure::create($validated);
        
        return redirect()->route('admin.inventory.units.index')->with('success', 'Unit of Measure created successfully.');
    }

    public function edit(UnitOfMeasure $unit)
    {
        $this->authorize('update', $unit);
        return view('admin.inventory.units.edit', compact('unit'));
    }

    public function update(Request $request, UnitOfMeasure $unit)
    {
        $this->authorize('update', $unit);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);
        
        $unit->update($validated);
        
        return redirect()->route('admin.inventory.units.index')->with('success', 'Unit of Measure updated successfully.');
    }

    public function destroy(UnitOfMeasure $unit)
    {
        $this->authorize('delete', $unit);
        $unit->delete();
        return redirect()->route('admin.inventory.units.index')->with('success', 'Unit of Measure deleted successfully.');
    }
}
