<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $this->authorize('update', $product);
        $variants = $product->variants()->latest()->get();
        return view('admin.inventory.products.variants.index', compact('product', 'variants'));
    }

    public function store(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'barcode' => 'nullable|string|max:255|unique:product_variants,barcode',
            'options' => 'required|array',
            'options.*' => 'required|string',
            'price_adjustment' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $product->variants()->create($validated);

        return redirect()->route('admin.inventory.products.variants.index', $product)
            ->with('success', 'Variant created successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $this->authorize('update', $product);
        $variant->delete();
        return redirect()->route('admin.inventory.products.variants.index', $product)
            ->with('success', 'Variant deleted successfully.');
    }
}
