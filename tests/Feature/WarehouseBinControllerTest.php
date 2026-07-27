<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\WarehouseBin;

class WarehouseBinControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Gate::before(fn () => true);
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active'
        ]);
        
        $role = \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $this->user->assignRole($role);
        
        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Test Warehouse',
            'status' => 'active'
        ]);
        
        $this->zone = WarehouseZone::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Test Zone',
            'status' => 'active'
        ]);
    }

    public function test_can_store_warehouse_bin_via_ui_form_fields()
    {
        $response = $this->actingAs($this->user)->post(route('admin.warehouse.bins.store'), [
            'warehouse_zone_id' => $this->zone->id,
            'code' => 'CS-A1-R1-S1',
            'aisle' => 'A1',
            'rack' => 'R1',
            'shelf' => 'S1',
            'capacity' => 100.50,
            'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.warehouse.bins.index'));
        
        $this->assertDatabaseHas('warehouse_bins', [
            'warehouse_zone_id' => $this->zone->id,
            'code' => 'CS-A1-R1-S1',
            'aisle' => 'A1',
            'rack' => 'R1',
            'shelf' => 'S1',
            'capacity' => 100.50,
            'status' => 'active'
        ]);
    }
    
    public function test_can_update_warehouse_bin_via_ui_form_fields()
    {
        $bin = WarehouseBin::create([
            'company_id' => $this->company->id,
            'warehouse_zone_id' => $this->zone->id,
            'code' => 'OLD-CODE',
            'status' => 'active',
            'capacity' => 10
        ]);
        
        $response = $this->actingAs($this->user)->put(route('admin.warehouse.bins.update', $bin), [
            'warehouse_zone_id' => $this->zone->id,
            'code' => 'NEW-CODE',
            'aisle' => 'B2',
            'rack' => 'R2',
            'shelf' => 'S2',
            'capacity' => 250,
            'status' => 'full'
        ]);

        $response->assertRedirect(route('admin.warehouse.bins.index'));
        
        $this->assertDatabaseHas('warehouse_bins', [
            'id' => $bin->id,
            'code' => 'NEW-CODE',
            'aisle' => 'B2',
            'rack' => 'R2',
            'shelf' => 'S2',
            'capacity' => 250,
            'status' => 'full'
        ]);
    }
}
