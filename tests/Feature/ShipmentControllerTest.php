<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehousePacking;
use App\Models\WarehousePicking;
use App\Models\WarehouseShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $warehouse;
    protected $shipment;

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
            'name' => 'Test WH',
            'code' => 'TWH',
            'status' => 'active',
        ]);

        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });

        $this->shipment = WarehouseShipment::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'shipment_number' => 'SHIP-TEST-001',
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_shipment_index()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.warehouse.shipments.index'));

        $response->assertStatus(200);
        $response->assertSee('SHIP-TEST-001');
    }

    public function test_can_create_shipment()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.warehouse.shipments.store'), [
            'warehouse_id' => $this->warehouse->id,
            'carrier' => 'FedEx',
            'tracking_number' => 'FX123456789',
            'shipping_notes' => 'Handle with care',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('warehouse_shipments', [
            'carrier' => 'FedEx',
            'tracking_number' => 'FX123456789',
            'status' => 'pending',
        ]);
    }

    public function test_can_prepare_shipment()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('admin.warehouse.shipments.update', $this->shipment), [
            'action' => 'prepare',
            'carrier' => 'DHL',
            'tracking_number' => 'DHL9876543',
        ]);

        $response->assertRedirect(route('admin.warehouse.shipments.show', $this->shipment));
        $this->assertDatabaseHas('warehouse_shipments', [
            'id' => $this->shipment->id,
            'status' => 'prepared',
            'carrier' => 'DHL',
            'tracking_number' => 'DHL9876543',
        ]);
    }

    public function test_can_dispatch_shipment()
    {
        $this->actingAs($this->user);

        $this->shipment->update(['status' => 'prepared', 'carrier' => 'UPS', 'tracking_number' => 'UPS111']);

        $response = $this->put(route('admin.warehouse.shipments.update', $this->shipment), [
            'action' => 'dispatch',
            'carrier' => 'UPS',
            'tracking_number' => 'UPS111',
        ]);

        $response->assertRedirect(route('admin.warehouse.shipments.show', $this->shipment));
        $this->assertDatabaseHas('warehouse_shipments', [
            'id' => $this->shipment->id,
            'status' => 'shipped',
        ]);
    }

    public function test_can_deliver_shipment()
    {
        $this->actingAs($this->user);

        $this->shipment->update(['status' => 'shipped']);

        $response = $this->put(route('admin.warehouse.shipments.update', $this->shipment), [
            'action' => 'deliver',
        ]);

        $response->assertRedirect(route('admin.warehouse.shipments.show', $this->shipment));
        $this->assertDatabaseHas('warehouse_shipments', [
            'id' => $this->shipment->id,
            'status' => 'delivered',
        ]);
    }

    public function test_can_cancel_shipment()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('admin.warehouse.shipments.update', $this->shipment), [
            'action' => 'cancel',
        ]);

        $response->assertRedirect(route('admin.warehouse.shipments.show', $this->shipment));
        $this->assertDatabaseHas('warehouse_shipments', [
            'id' => $this->shipment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_shipment_show_displays_details()
    {
        $this->actingAs($this->user);

        $this->shipment->update(['carrier' => 'TNT', 'tracking_number' => 'TNT555']);

        $response = $this->get(route('admin.warehouse.shipments.show', $this->shipment));

        $response->assertStatus(200);
        $response->assertSee('SHIP-TEST-001');
        $response->assertSee('TNT');
        $response->assertSee('TNT555');
    }
}
