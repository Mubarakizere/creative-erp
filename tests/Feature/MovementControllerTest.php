<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseMovement;
use App\Models\WarehouseZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $zoneA;
    protected $binA;
    protected $zoneB;
    protected $binB;
    protected $product;
    protected $inventory;

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

        $this->zoneA = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Zone A',
            'type' => 'storage',
        ]);
        $this->binA = WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $this->zoneA->id,
            'code' => 'A-01',
            'capacity' => 100,
        ]);

        $this->zoneB = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Zone B',
            'type' => 'storage',
        ]);
        $this->binB = WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $this->zoneB->id,
            'code' => 'B-01',
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

        $this->inventory = Inventory::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $this->zoneA->id,
            'warehouse_bin_id' => $this->binA->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'available_quantity' => 10,
            'unit_cost' => 5.00,
            'valuation_method' => 'fifo',
        ]);

        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_movement_index()
    {
        WarehouseMovement::create([
            'company_id' => $this->company->id,
            'movement_number' => 'MOV-TEST',
            'type' => 'bin_to_bin',
            'status' => 'pending',
            'source_warehouse_id' => $this->warehouse->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.warehouse.movements.index'));
        $response->assertStatus(200);
        $response->assertSee('MOV-TEST');
    }

    public function test_can_request_movement()
    {
        $response = $this->actingAs($this->user)->post(route('admin.warehouse.movements.store'), [
            'type' => 'bin_to_bin',
            'source_warehouse_id' => $this->warehouse->id,
            'source_zone_id' => $this->zoneA->id,
            'source_bin_id' => $this->binA->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'destination_zone_id' => $this->zoneB->id,
            'destination_bin_id' => $this->binB->id,
            'product_id' => $this->product->id,
            'quantity' => 4.5,
            'reason' => 'Need space',
        ]);

        $movement = WarehouseMovement::first();
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('warehouse_movements', [
            'type' => 'bin_to_bin',
            'quantity' => 4.5,
            'status' => 'pending',
            'reason' => 'Need space',
        ]);
    }

    public function test_can_execute_movement()
    {
        $movement = WarehouseMovement::create([
            'company_id' => $this->company->id,
            'movement_number' => 'MOV-EXEC',
            'type' => 'bin_to_bin',
            'status' => 'pending',
            'source_warehouse_id' => $this->warehouse->id,
            'source_zone_id' => $this->zoneA->id,
            'source_bin_id' => $this->binA->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'destination_zone_id' => $this->zoneB->id,
            'destination_bin_id' => $this->binB->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.movements.update', $movement), [
            'action' => 'execute'
        ]);

        $response->assertStatus(302);
        
        // Assert movement status updated
        $this->assertDatabaseHas('warehouse_movements', [
            'id' => $movement->id,
            'status' => 'completed',
            'approved_by' => $this->user->id,
        ]);

        // Assert source inventory decreased
        $this->assertDatabaseHas('inventories', [
            'id' => $this->inventory->id,
            'available_quantity' => 7, // 10 - 3
        ]);

        // Assert destination inventory increased/created
        $this->assertDatabaseHas('inventories', [
            'warehouse_bin_id' => $this->binB->id,
            'product_id' => $this->product->id,
            'available_quantity' => 3,
        ]);

        // Assert transaction history created
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'transfer_out',
            'quantity' => -3,
            'reference_id' => $movement->id,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'type' => 'transfer_in',
            'quantity' => 3,
            'reference_id' => $movement->id,
        ]);
    }

    public function test_can_cancel_movement()
    {
        $movement = WarehouseMovement::create([
            'company_id' => $this->company->id,
            'movement_number' => 'MOV-CANCEL',
            'type' => 'bin_to_bin',
            'status' => 'pending',
            'source_warehouse_id' => $this->warehouse->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.movements.update', $movement), [
            'action' => 'cancel'
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('warehouse_movements', [
            'id' => $movement->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_execute_with_insufficient_stock()
    {
        $this->withoutExceptionHandling();

        $movement = WarehouseMovement::create([
            'company_id' => $this->company->id,
            'movement_number' => 'MOV-FAIL',
            'type' => 'bin_to_bin',
            'status' => 'pending',
            'source_warehouse_id' => $this->warehouse->id,
            'source_zone_id' => $this->zoneA->id,
            'source_bin_id' => $this->binA->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'destination_zone_id' => $this->zoneB->id,
            'destination_bin_id' => $this->binB->id,
            'product_id' => $this->product->id,
            'quantity' => 20, // We only have 10
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->actingAs($this->user)->put(route('admin.warehouse.movements.update', $movement), [
            'action' => 'execute'
        ]);
    }
}
