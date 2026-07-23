<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\UnitOfMeasure;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $companyId = session('company_id') ?? 1;

        $query = Product::where('company_id', $companyId)->with(['category', 'brand', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.inventory.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $companyId = session('company_id') ?? 1;

        $categories = ProductCategory::where('company_id', $companyId)->get();
        $brands = Brand::where('company_id', $companyId)->get();
        $uoms = UnitOfMeasure::where('company_id', $companyId)->get();
        $taxes = Tax::where('company_id', $companyId)->get();

        return view('admin.inventory.products.create', compact('categories', 'brands', 'uoms', 'taxes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        $companyId = session('company_id') ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'type' => 'required|in:physical,service,raw_material,finished_good',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_of_measure_id' => 'nullable|exists:unit_of_measures,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'reorder_level' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'track_inventory' => 'boolean',
            'allow_negative_stock' => 'boolean',
            'serial_numbers' => 'boolean',
            'batch_numbers' => 'boolean',
            'expiration_date' => 'boolean',
            'description' => 'nullable|string',
            'valuation_method' => 'required|in:Standard Cost,Weighted Average,FIFO',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['company_id'] = $companyId;
        $validated['track_inventory'] = $request->boolean('track_inventory');
        $validated['allow_negative_stock'] = $request->boolean('allow_negative_stock');
        $validated['serial_numbers'] = $request->boolean('serial_numbers');
        $validated['batch_numbers'] = $request->boolean('batch_numbers');
        $validated['expiration_date'] = $request->boolean('expiration_date');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.inventory.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $companyId = session('company_id') ?? 1;

        $categories = ProductCategory::where('company_id', $companyId)->get();
        $brands = Brand::where('company_id', $companyId)->get();
        $uoms = UnitOfMeasure::where('company_id', $companyId)->get();
        $taxes = Tax::where('company_id', $companyId)->get();

        return view('admin.inventory.products.edit', compact('product', 'categories', 'brands', 'uoms', 'taxes'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'type' => 'required|in:physical,service,raw_material,finished_good',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_of_measure_id' => 'nullable|exists:unit_of_measures,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'reorder_level' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'track_inventory' => 'boolean',
            'allow_negative_stock' => 'boolean',
            'serial_numbers' => 'boolean',
            'batch_numbers' => 'boolean',
            'expiration_date' => 'boolean',
            'description' => 'nullable|string',
            'valuation_method' => 'required|in:Standard Cost,Weighted Average,FIFO',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048'
        ]);

        $validated['track_inventory'] = $request->boolean('track_inventory');
        $validated['allow_negative_stock'] = $request->boolean('allow_negative_stock');
        $validated['serial_numbers'] = $request->boolean('serial_numbers');
        $validated['batch_numbers'] = $request->boolean('batch_numbers');
        $validated['expiration_date'] = $request->boolean('expiration_date');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.inventory.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.inventory.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
