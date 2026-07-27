<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehousePicking;
use App\Models\WarehouseTask;
use App\Models\Inventory;
use App\Models\WarehouseShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PickingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $bin;
    protected $product;
    protected $picking;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active'
        ]);
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $this->user->assignRole($role);
        
        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Main WH',
            'code' => 'WH1',
            'status' => 'active'
        ]);

        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        $zone = \App\Models\WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Test Zone',
            'code' => 'TZ1',
            'type' => 'storage'
        ]);

        $this->bin = WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'code' => 'A1-01',
            'status' => 'active'
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'type' => 'goods',
            'sku' => 'SKU-001',
        ]);

        // Seed Inventory
        Inventory::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'available_quantity' => 10,
            'unit_cost' => 5.00
        ]);

        $shipment = WarehouseShipment::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'shipment_number' => 'SHP-123',
        ]);

        $this->picking = WarehousePicking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'picking_number' => 'PICK-001',
            'pickable_type' => WarehouseShipment::class,
            'pickable_id' => $shipment->id,
        ]);

        $this->task = WarehouseTask::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'picking',
            'status' => 'pending',
            'taskable_type' => WarehousePicking::class,
            'taskable_id' => $this->picking->id,
            'notes' => json_encode([
                'product_id' => $this->product->id,
                'bin_id' => $this->bin->id,
                'quantity' => 5
            ]),
        ]);

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_picking_index()
    {
        // Mock authorization
        $this->actingAs($this->user);

        $response = $this->get(route('admin.warehouse.picking.index'));

        $response->assertStatus(200);
        $response->assertSee('PICK-001');
    }

    public function test_can_execute_full_pick()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('admin.warehouse.picking.update', $this->task), [
            'picked_quantity' => 5,
        ]);

        $response->assertRedirect(route('admin.warehouse.picking.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('warehouse_tasks', [
            'id' => $this->task->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('inventories', [
            'warehouse_bin_id' => $this->bin->id,
            'available_quantity' => 5, // 10 original - 5 picked
        ]);
    }

    public function test_can_execute_partial_pick()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('admin.warehouse.picking.update', $this->task), [
            'picked_quantity' => 2,
        ]);

        $response->assertRedirect(route('admin.warehouse.picking.index'));
        $response->assertSessionHas('success');

        $this->task->refresh();
        $this->assertEquals('pending', $this->task->status);
        
        $data = json_decode($this->task->notes, true);
        $this->assertEquals(3, $data['quantity']); // 5 requested - 2 picked = 3 remaining

        $this->assertDatabaseHas('inventories', [
            'warehouse_bin_id' => $this->bin->id,
            'available_quantity' => 8, // 10 original - 2 picked
        ]);
    }
}
