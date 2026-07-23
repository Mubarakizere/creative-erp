<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ProductCategory::class);
        $companyId = session('company_id') ?? 1;
        $categories = ProductCategory::where('company_id', $companyId)->latest()->paginate(15);
        
        return view('admin.inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', ProductCategory::class);
        $companyId = session('company_id') ?? 1;
        $parentCategories = ProductCategory::where('company_id', $companyId)->whereNull('parent_id')->get();
        return view('admin.inventory.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ProductCategory::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id'
        ]);
        
        $validated['company_id'] = session('company_id') ?? 1;
        ProductCategory::create($validated);
        
        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ProductCategory $category)
    {
        $this->authorize('update', $category);
        $companyId = session('company_id') ?? 1;
        $parentCategories = ProductCategory::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
            
        return view('admin.inventory.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $this->authorize('update', $category);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id|not_in:' . $category->id
        ]);
        
        $category->update($validated);
        
        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return redirect()->route('admin.inventory.categories.index')->with('success', 'Category deleted successfully.');
    }
}
