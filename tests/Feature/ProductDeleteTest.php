<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $this->user->assignRole($role);

        $category = ProductCategory::create(['name' => 'Materials', 'company_id' => $this->company->id]);
        $uom = UnitOfMeasure::create(['name' => 'Piece', 'abbreviation' => 'pcs', 'company_id' => $this->company->id]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Delete Test Material',
            'sku' => 'DEL-001',
            'product_category_id' => $category->id,
            'unit_of_measure_id' => $uom->id,
            'type' => 'raw_material',
            'cost_price' => 100,
        ]);
    }

    public function test_can_soft_delete_product()
    {
        $response = $this->actingAs($this->user)->delete(route('admin.inventory.products.destroy', $this->product));

        $response->assertRedirect(route('admin.inventory.products.index'));
        $response->assertSessionHas('success', 'Material deleted successfully.');

        $this->assertSoftDeleted('products', [
            'id' => $this->product->id,
        ]);
    }
}
