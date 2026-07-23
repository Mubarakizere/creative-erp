<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Brand::class);
        $companyId = session('company_id') ?? 1;
        $brands = Brand::where('company_id', $companyId)->latest()->paginate(15);
        return view('admin.inventory.brands.index', compact('brands'));
    }

    public function create()
    {
        $this->authorize('create', Brand::class);
        return view('admin.inventory.brands.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Brand::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048'
        ]);
        
        $validated['company_id'] = session('company_id') ?? 1;
        
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }
        
        Brand::create($validated);
        
        return redirect()->route('admin.inventory.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        $this->authorize('update', $brand);
        return view('admin.inventory.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048'
        ]);
        
        if ($request->hasFile('logo')) {
            if ($brand->logo) Storage::disk('public')->delete($brand->logo);
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }
        
        $brand->update($validated);
        
        return redirect()->route('admin.inventory.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);
        if ($brand->logo) Storage::disk('public')->delete($brand->logo);
        $brand->delete();
        return redirect()->route('admin.inventory.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
