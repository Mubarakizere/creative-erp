<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseCycleCount;
use App\Models\WarehouseZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CycleCountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $zone;
    protected $bin;
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

        $this->inventory = Inventory::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'warehouse_bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'available_quantity' => 100,
            'valuation_method' => 'FIFO',
            'unit_cost' => 10.00,
        ]);

        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_cycle_counts_index()
    {
        $response = $this->actingAs($this->user)->get(route('admin.warehouse.cycle-counts.index'));
        $response->assertStatus(200);
    }

    public function test_can_initiate_cycle_count()
    {
        $response = $this->actingAs($this->user)->post(route('admin.warehouse.cycle-counts.store'), [
            'warehouse_id' => $this->warehouse->id,
            'type' => 'manual',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('warehouse_cycle_counts', [
            'type' => 'manual',
            'status' => 'pending',
            'warehouse_id' => $this->warehouse->id,
        ]);
    }

    public function test_can_record_count_with_no_variance()
    {
        $cycleCountService = app(\App\Services\Warehouse\CycleCountService::class);
        $count = $cycleCountService->initiateCount([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'manual',
        ], $this->user->id);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.cycle-counts.update', $count), [
            'action' => 'record',
            'items' => [
                [
                    'inventory_id' => $this->inventory->id,
                    'counted_quantity' => 100, // Matches available_quantity
                ]
            ]
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('warehouse_cycle_counts', [
            'id' => $count->id,
            'status' => 'completed',
        ]);
        
        $this->assertDatabaseHas('stock_count_items', [
            'inventory_id' => $this->inventory->id,
            'expected_quantity' => 100,
            'counted_quantity' => 100,
            'variance' => 0,
        ]);
    }

    public function test_can_record_count_with_variance()
    {
        $cycleCountService = app(\App\Services\Warehouse\CycleCountService::class);
        $count = $cycleCountService->initiateCount([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'manual',
        ], $this->user->id);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.cycle-counts.update', $count), [
            'action' => 'record',
            'items' => [
                [
                    'inventory_id' => $this->inventory->id,
                    'counted_quantity' => 95, // 5 missing
                ]
            ]
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('warehouse_cycle_counts', [
            'id' => $count->id,
            'status' => 'variance_detected',
        ]);
        
        $this->assertDatabaseHas('stock_count_items', [
            'inventory_id' => $this->inventory->id,
            'expected_quantity' => 100,
            'counted_quantity' => 95,
            'variance' => -5,
        ]);
    }

    public function test_can_approve_variance_and_adjust_inventory()
    {
        $cycleCountService = app(\App\Services\Warehouse\CycleCountService::class);
        $count = $cycleCountService->initiateCount([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'manual',
        ], $this->user->id);

        $cycleCountService->recordCount($count, [
            [
                'inventory_id' => $this->inventory->id,
                'counted_quantity' => 95, // -5 variance
            ]
        ], $this->user->id);

        $this->assertEquals('variance_detected', $count->fresh()->status);

        // Act: Approve it
        $response = $this->actingAs($this->user)->put(route('admin.warehouse.cycle-counts.update', $count), [
            'action' => 'approve',
        ]);

        $response->assertStatus(302);

        // Assert count approved
        $this->assertDatabaseHas('warehouse_cycle_counts', [
            'id' => $count->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);

        // Assert inventory updated
        $this->assertDatabaseHas('inventories', [
            'id' => $this->inventory->id,
            'available_quantity' => 95,
        ]);

        // Assert adjustment record created
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $this->inventory->id,
            'type' => 'cycle_count_deduction',
            'quantity' => 5,
        ]);
    }
}
