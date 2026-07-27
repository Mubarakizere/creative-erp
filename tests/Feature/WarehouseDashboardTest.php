<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseZone;
use App\Models\WarehousePicking;
use App\Models\WarehousePacking;
use App\Models\WarehouseShipment;
use App\Models\WarehouseReturn;
use App\Models\WarehouseTask;
use App\Models\WarehouseCycleCount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

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
        
        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_warehouse_dashboard_and_metrics()
    {
        // 1. Setup Base Warehouse Data
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Main WH',
            'code' => 'MWH',
            'status' => 'active',
        ]);

        $zone = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'name' => 'Zone A',
            'type' => 'storage',
        ]);
        
        // 2. Setup Bin Data for Capacity & Utilization
        // Bin 1: Full
        WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'code' => 'FULL-01',
            'capacity' => 100,
            'current_quantity' => 100,
            'status' => 'full'
        ]);
        
        // Bin 2: Partial
        WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'code' => 'PARTIAL-01',
            'capacity' => 100,
            'current_quantity' => 50,
            'status' => 'active'
        ]);
        
        // Bin 3: Empty
        WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_zone_id' => $zone->id,
            'code' => 'EMPTY-01',
            'capacity' => 100,
            'current_quantity' => 0,
            'status' => 'active'
        ]);

        // Total Capacity: 300. Current Quantity: 150. Utilization: 50%

        // 3. Setup Pending Operations
        $picking = WarehousePicking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'picking_number' => 'PICK-001',
            'type' => 'sales_order',
            'pickable_type' => 'App\Models\SalesOrder',
            'pickable_id' => '00000000-0000-0000-0000-000000000000'
        ]);
        
        WarehousePacking::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_picking_id' => $picking->id,
            'status' => 'pending',
            'packing_number' => 'PACK-001'
        ]);
        
        WarehouseShipment::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'ready',
            'shipment_number' => 'SHIP-001'
        ]);
        
        WarehouseReturn::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'inspecting',
            'return_number' => 'RET-001',
            'type' => 'customer',
            'returnable_type' => 'App\Models\SalesOrder',
            'returnable_id' => '00000000-0000-0000-0000-000000000000'
        ]);
        
        WarehouseTask::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'type' => 'cleaning',
            'taskable_type' => 'App\Models\WarehouseBin',
            'taskable_id' => '00000000-0000-0000-0000-000000000000'
        ]);
        
        $stockCount = \App\Models\StockCount::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'cycle_count',
            'status' => 'pending'
        ]);
        
        WarehouseCycleCount::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $warehouse->id,
            'stock_count_id' => $stockCount->id,
            'status' => 'variance_detected',
            'type' => 'manual',
            'count_number' => 'CC-001'
        ]);

        // 4. Act & Assert
        $response = $this->actingAs($this->user)->get(route('admin.warehouse.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.warehouse.dashboard.index');
        
        $metrics = $response->viewData('metrics');
        
        $this->assertEquals(50, $metrics['utilization']);
        $this->assertEquals(1, $metrics['bin_capacity']['full']);
        $this->assertEquals(1, $metrics['bin_capacity']['partial']);
        $this->assertEquals(1, $metrics['bin_capacity']['empty']);
        $this->assertEquals(3, $metrics['bin_capacity']['total']);
        
        $this->assertEquals(1, $metrics['pending_picks']);
        $this->assertEquals(1, $metrics['pending_packing']);
        $this->assertEquals(1, $metrics['pending_shipments']);
        $this->assertEquals(1, $metrics['pending_returns']);
        $this->assertEquals(1, $metrics['warehouse_tasks']);
        $this->assertEquals(1, $metrics['cycle_counts']);
    }
}
