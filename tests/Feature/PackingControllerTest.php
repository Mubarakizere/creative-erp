<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehousePicking;
use App\Models\WarehousePacking;
use App\Models\WarehouseTask;
use App\Models\Inventory;
use App\Models\WarehouseShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $bin;
    protected $product;
    protected $picking;
    protected $packing;

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
            'status' => 'completed',
        ]);

        $this->packing = WarehousePacking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_picking_id' => $this->picking->id,
            'packing_number' => 'PACK-001',
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_packing_index()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.warehouse.packing.index'));

        $response->assertStatus(200);
        $response->assertSee('PACK-001');
    }

    public function test_can_create_packing_from_picking()
    {
        $this->actingAs($this->user);

        // Delete existing packing to test creation
        $this->packing->delete();

        $response = $this->post(route('admin.warehouse.packing.store'), [
            'picking_id' => $this->picking->id,
        ]);

        $newPacking = WarehousePacking::latest()->first();

        $response->assertRedirect(route('admin.warehouse.packing.edit', $newPacking));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('warehouse_packings', [
            'warehouse_picking_id' => $this->picking->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_execute_packing_completion()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('admin.warehouse.packing.update', $this->packing), [
            'total_weight' => 15.5,
            'length' => 10,
            'width' => 20,
            'height' => 30,
            'notes' => 'Fragile box'
        ]);

        $response->assertRedirect(route('admin.warehouse.packing.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('warehouse_packings', [
            'id' => $this->packing->id,
            'status' => 'completed',
            'total_weight' => 15.5,
            'length' => 10,
            'width' => 20,
            'height' => 30,
            'notes' => 'Fragile box',
        ]);
    }
}
