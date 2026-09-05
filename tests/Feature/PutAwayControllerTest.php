<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\WarehouseBin;
use App\Models\Product;
use App\Models\GoodsReceiptItem;
use App\Models\WarehouseTask;
use App\Models\Inventory;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class PutAwayControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $warehouse;
    protected $zone;
    protected $bin;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active'
        ]);
        
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $this->user->assignRole($role);

        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
            'status' => 'active',
            'is_default' => true
        ]);

        $this->zone = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Receiving Zone',
            'code' => 'REC',
            'type' => 'receiving',
            'status' => 'active'
        ]);

        $this->bin = WarehouseBin::create([
            'warehouse_zone_id' => $this->zone->id,
            'company_id' => $this->company->id,
            'code' => 'REC-01',
            'capacity' => 100, // Small capacity
            'current_quantity' => 0,
            'status' => 'active'
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'type' => 'raw_material',
            'cost_price' => 10,
        ]);
    }

    public function test_can_view_put_away_tasks_index()
    {
        $gr = \App\Models\GoodsReceipt::create([
            'company_id' => $this->company->id,
            'code' => 'GR-001',
            'warehouse_id' => $this->warehouse->id,
            'supplier_id' => \App\Models\Supplier::create(['company_id' => $this->company->id, 'name' => 'Test Supplier', 'code' => 'SUP01'])->id,
            'receipt_date' => now(),
            'status' => 'completed',
        ]);

        $task = WarehouseTask::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'put_away',
            'status' => 'pending',
            'taskable_type' => GoodsReceiptItem::class,
            'taskable_id' => GoodsReceiptItem::create(['goods_receipt_id' => $gr->id, 'product_id' => $this->product->id, 'quantity' => 10, 'quantity_received' => 10])->id,
            'priority' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.warehouse.put-away.index'));
        $response->assertStatus(200);
        $response->assertSee('#'.$task->id);
    }

    public function test_can_execute_put_away_and_validation_prevents_overfilling()
    {
        // Setup Goods Receipt
        $gr = \App\Models\GoodsReceipt::create([
            'company_id' => $this->company->id,
            'code' => 'GR-002',
            'warehouse_id' => $this->warehouse->id,
            'supplier_id' => \App\Models\Supplier::create(['company_id' => $this->company->id, 'name' => 'Test Supplier 2', 'code' => 'SUP02'])->id,
            'receipt_date' => now(),
            'status' => 'completed',
        ]);

        // Setup Goods Receipt Item
        $grItem = GoodsReceiptItem::create([
            'goods_receipt_id' => $gr->id,
            'product_id' => $this->product->id,
            'quantity' => 150, // More than bin capacity (100)
            'quantity_received' => 150,
            'unit_price' => 10,
        ]);

        $task = WarehouseTask::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'put_away',
            'status' => 'pending',
            'taskable_type' => GoodsReceiptItem::class,
            'taskable_id' => $grItem->id,
            'priority' => 1,
        ]);

        // Generic inventory created by Goods Receipt
        Inventory::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'available_quantity' => 150,
            'incoming_quantity' => 150,
        ]);

        // Test overfilling validation
        $response = $this->actingAs($this->user)->put(route('admin.warehouse.put-away.update', $task), [
            'warehouse_bin_id' => $this->bin->id
        ]);
        
        $response->assertSessionHasErrors(['warehouse_bin_id']);

        // Test successful put away (update bin capacity so it fits)
        $this->bin->update(['capacity' => 200]);

        $response = $this->actingAs($this->user)->put(route('admin.warehouse.put-away.update', $task), [
            'warehouse_bin_id' => $this->bin->id
        ]);

        $response->assertRedirect(route('admin.warehouse.put-away.index'));
        $response->assertSessionHas('success');

        // Verify task status
        $this->assertDatabaseHas('warehouse_tasks', [
            'id' => $task->id,
            'status' => 'completed'
        ]);

        // Verify Bin quantity updated
        $this->assertDatabaseHas('warehouse_bins', [
            'id' => $this->bin->id,
            'current_quantity' => 150
        ]);

        // Verify generic inventory decremented
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'warehouse_zone_id' => null,
            'warehouse_bin_id' => null,
            'available_quantity' => 0
        ]);

        // Verify bin-level inventory created
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'warehouse_bin_id' => $this->bin->id,
            'available_quantity' => 150
        ]);
    }
}
