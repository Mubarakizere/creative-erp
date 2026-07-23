<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\UnitOfMeasure;

class ProductService
{
    public function createProduct(array $data)
    {
        return Product::create($data);
    }
    
    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }

    public function createCategory(array $data)
    {
        return ProductCategory::create($data);
    }

    public function createBrand(array $data)
    {
        return Brand::create($data);
    }

    public function createUnitOfMeasure(array $data)
    {
        return UnitOfMeasure::create($data);
    }
}
