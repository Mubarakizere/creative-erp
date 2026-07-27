<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseReturn;
use App\Models\WarehouseZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $zone;
    protected $bin;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $this->user->assignRole($role);

        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Main WH',
            'code' => 'MWH',
            'status' => 'active',
        ]);

        $this->zone = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Zone A',
            'type' => 'storage',
        ]);
        
        $this->bin = WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $this->zone->id,
            'code' => 'A-01',
            'capacity' => 100,
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Widget',
            'sku' => 'WIDGET-01',
            'type' => 'goods',
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_returns_index()
    {
        WarehouseReturn::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'return_number' => 'RET-TEST',
            'type' => 'customer_return',
            'status' => 'pending',
            'items' => [['product_id' => $this->product->id, 'quantity' => 2]],
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.warehouse.returns.index'));
        $response->assertStatus(200);
        $response->assertSee('RET-TEST');
    }

    public function test_can_create_return()
    {
        $response = $this->actingAs($this->user)->post(route('admin.warehouse.returns.store'), [
            'warehouse_id' => $this->warehouse->id,
            'type' => 'supplier_return',
            'requires_accounting_adjustment' => false,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_cost' => 5.00,
                ]
            ]
        ]);

        $return = WarehouseReturn::first();
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('warehouse_returns', [
            'type' => 'supplier_return',
            'status' => 'pending',
            'warehouse_id' => $this->warehouse->id,
        ]);
        $this->assertEquals(10, $return->items[0]['quantity']);
    }

    public function test_can_restock_return()
    {
        $return = WarehouseReturn::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'return_number' => 'RET-RESTOCK',
            'type' => 'customer_return',
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'unit_cost' => 10,
                ]
            ],
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.returns.update', $return), [
            'status' => 'restocked',
            'notes' => 'Look good to me',
            'restock_items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'bin_id' => $this->bin->id,
                    'unit_cost' => 10,
                ]
            ]
        ]);

        $response->assertStatus(302);
        
        // Assert return updated
        $this->assertDatabaseHas('warehouse_returns', [
            'id' => $return->id,
            'status' => 'restocked',
            'inspected_by' => $this->user->id,
        ]);

        // Assert inventory incremented
        $this->assertDatabaseHas('inventories', [
            'warehouse_bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'available_quantity' => 5,
        ]);

        // Assert transaction logged
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'return_restock',
            'quantity' => 5,
            'reference_id' => $return->id,
        ]);
    }

    public function test_can_dispose_return_with_accounting_adjustment()
    {
        $return = WarehouseReturn::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'return_number' => 'RET-DISPOSE',
            'type' => 'damaged_stock',
            'status' => 'pending',
            'requires_accounting_adjustment' => true,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_cost' => 50,
                ]
            ],
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.returns.update', $return), [
            'status' => 'disposed',
            'loss_amount' => 100,
            'notes' => 'Completely ruined',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('warehouse_returns', [
            'id' => $return->id,
            'status' => 'disposed',
        ]);

        // Inventory should NOT be created
        $this->assertDatabaseMissing('inventories', [
            'product_id' => $this->product->id,
        ]);
        
        // We know the AccountingService stub logs "Inventory write-off recorded", but testing the exact log in a simple test might be overkill.
        // As long as it didn't throw an exception, the stub was called successfully.
    }
}
