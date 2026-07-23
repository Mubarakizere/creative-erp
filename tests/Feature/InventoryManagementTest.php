<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\UnitOfMeasure;
use App\Models\Warehouse;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        // Ensure user has necessary permissions
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $this->user->assignRole($role);
    }

    public function test_can_create_product()
    {
        $category = ProductCategory::create(['name' => 'Electronics', 'company_id' => $this->company->id]);
        $uom = UnitOfMeasure::create(['name' => 'Pieces', 'abbreviation' => 'pcs', 'company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->postJson('/api/inventory/products', [
            'name' => 'Laptop',
            'sku' => 'LP-001',
            'product_category_id' => $category->id,
            'unit_of_measure_id' => $uom->id,
            'type' => 'goods',
            'cost_price' => 500,
            'selling_price' => 1000,
            'track_inventory' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Laptop']);

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop',
            'sku' => 'LP-001'
        ]);
    }

    public function test_can_create_warehouse()
    {
        $response = $this->actingAs($this->user)->postJson('/api/inventory/warehouses', [
            'name' => 'Main Warehouse',
            'code' => 'MW-01',
            'status' => 'active',
            'is_default' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Main Warehouse']);
    }

    public function test_can_process_inventory_transfer()
    {
        $category = ProductCategory::create(['name' => 'Electronics', 'company_id' => $this->company->id]);
        $uom = UnitOfMeasure::create(['name' => 'Pieces', 'abbreviation' => 'pcs', 'company_id' => $this->company->id]);
        $product = Product::create([
            'name' => 'Laptop',
            'sku' => 'LAP-123',
            'company_id' => $this->company->id,
            'product_category_id' => $category->id,
            'unit_of_measure_id' => $uom->id,
            'type' => 'goods',
            'selling_price' => 1000,
        ]);

        $w1 = Warehouse::create(['name' => 'W1', 'company_id' => $this->company->id, 'status' => 'active']);
        $w2 = Warehouse::create(['name' => 'W2', 'company_id' => $this->company->id, 'status' => 'active']);

        // Stock in W1
        $engine = app(\App\Services\Inventory\InventoryEngine::class);
        $engine->stockIn($product, $w1, 50, 'initial_stock');

        // Transfer 10 from W1 to W2
        $response = $this->actingAs($this->user)->postJson('/api/inventory/transfer', [
            'from_warehouse_id' => $w1->id,
            'to_warehouse_id' => $w2->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10]
            ],
            'reason' => 'Restocking W2'
        ]);

        $response->assertStatus(201);

        // Assert Inventory Transfer document exists
        $this->assertDatabaseHas('inventory_transfers', [
            'from_warehouse_id' => $w1->id,
            'to_warehouse_id' => $w2->id,
            'status' => 'completed' // auto-approved and completed by engine
        ]);

        // Assert Inventory values
        $this->assertEquals(40, Inventory::where('warehouse_id', $w1->id)->where('product_id', $product->id)->first()->available_quantity);
        $this->assertEquals(10, Inventory::where('warehouse_id', $w2->id)->where('product_id', $product->id)->first()->available_quantity);
    }
}
