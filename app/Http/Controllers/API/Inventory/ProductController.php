<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Inventory\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)
            ->with(['category', 'brand', 'unit'])
            ->paginate($request->get('per_page', 15));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'product_category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_of_measure_id' => 'required|exists:unit_of_measures,id',
            'type' => 'required|in:goods,service',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        $product = $this->productService->createProduct($validated);

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'brand', 'unit', 'variants', 'barcodes', 'inventory.warehouse']);
        return response()->json($product);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'nullable|string|max:100',
            'product_category_id' => 'sometimes|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_of_measure_id' => 'sometimes|exists:unit_of_measures,id',
            'type' => 'sometimes|in:goods,service',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
        ]);

        $product = $this->productService->updateProduct($product, $validated);

        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);
        return response()->json(null, 204);
    }
}
