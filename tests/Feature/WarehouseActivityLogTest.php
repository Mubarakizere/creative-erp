<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\WarehouseBin;
use App\Models\WarehouseMovement;
use App\Models\WarehouseTask;
use App\Models\WarehousePicking;
use App\Models\WarehousePacking;
use App\Models\WarehouseShipment;
use App\Models\WarehouseReturn;
use App\Models\WarehouseCycleCount;
use App\Models\ActivityLog;
use App\Models\Product;

class WarehouseActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::create(['company_id' => $this->company->id, 'name' => 'Main Warehouse', 'code' => 'WH01']);
        
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);
    }

    public function test_bin_assignment_creates_audit_log()
    {
        $zone = WarehouseZone::create(['warehouse_id' => $this->warehouse->id, 'name' => 'Zone A', 'company_id' => $this->company->id]);
        $bin = WarehouseBin::create([
            'warehouse_id' => $this->warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'company_id' => $this->company->id,
            'code' => 'BIN-001',
            'status' => 'active',
            'capacity' => 100
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseBin::class,
            'subject_id' => $bin->id,
            'action' => 'created'
        ]);
        
        $bin->update(['status' => 'inactive']);
        
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseBin::class,
            'subject_id' => $bin->id,
            'action' => 'updated'
        ]);
    }

    public function test_put_away_operation_creates_audit_log()
    {
        $task = WarehouseTask::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'put_away',
            'status' => 'pending',
            'taskable_type' => 'App\Models\WarehouseMovement',
            'taskable_id' => '00000000-0000-0000-0000-000000000000'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseTask::class,
            'subject_id' => $task->id,
            'action' => 'created'
        ]);
    }
    
    public function test_picking_creates_audit_log()
    {
        $picking = WarehousePicking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'picking_number' => 'PICK-001',
            'status' => 'pending',
            'pickable_type' => 'App\Models\WarehouseShipment',
            'pickable_id' => '00000000-0000-0000-0000-000000000000'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehousePicking::class,
            'subject_id' => $picking->id,
            'action' => 'created'
        ]);
    }
    
    public function test_packing_creates_audit_log()
    {
        $packing = WarehousePacking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'packing_number' => 'PACK-001',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehousePacking::class,
            'subject_id' => $packing->id,
            'action' => 'created'
        ]);
    }
    
    public function test_shipment_creates_audit_log()
    {
        $shipment = WarehouseShipment::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'shipment_number' => 'SHIP-001',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseShipment::class,
            'subject_id' => $shipment->id,
            'action' => 'created'
        ]);
    }
    
    public function test_movement_creates_audit_log()
    {
        $product = Product::create(['company_id' => $this->company->id, 'name' => 'Prod', 'sku' => 'PROD-001', 'type' => 'goods']);
        
        $movement = WarehouseMovement::create([
            'company_id' => $this->company->id,
            'product_id' => $product->id,
            'source_warehouse_id' => $this->warehouse->id,
            'destination_warehouse_id' => $this->warehouse->id,
            'movement_number' => 'MOV-001',
            'quantity' => 10,
            'type' => 'transfer',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseMovement::class,
            'subject_id' => $movement->id,
            'action' => 'created'
        ]);
    }
    
    public function test_return_creates_audit_log()
    {
        $return = WarehouseReturn::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'return_number' => 'RET-001',
            'type' => 'customer',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseReturn::class,
            'subject_id' => $return->id,
            'action' => 'created'
        ]);
    }
    
    public function test_cycle_count_creates_audit_log()
    {
        $count = WarehouseCycleCount::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'count_number' => 'CC-001',
            'type' => 'manual',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => WarehouseCycleCount::class,
            'subject_id' => $count->id,
            'action' => 'created'
        ]);
    }
}
