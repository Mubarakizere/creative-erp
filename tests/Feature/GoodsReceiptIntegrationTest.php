<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoodsReceiptIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup base data
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        // Create permissions
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'procurement.view']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'goods_receipt.create']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'purchase_order.view']);

        // Create role and assign
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $this->user->assignRole($role);

        // Give permissions
        $this->user->givePermissionTo('procurement.view');
        $this->user->givePermissionTo('goods_receipt.create');
        $this->user->givePermissionTo('purchase_order.view');
        
        $this->warehouse = Warehouse::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'company_id' => $this->company->id,
            'name' => 'Main Warehouse',
            'status' => 'active',
        ]);
        
        $this->supplier = Supplier::create([
            'company_id' => $this->company->id,
            'name' => 'Test Supplier',
            'code' => 'SUP-001',
            'email' => 'supplier@example.com',
            'phone' => '1234567890',
        ]);
        
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'type' => 'raw_material',
            'cost_price' => 10,
        ]);

        $this->po = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'code' => 'PO-TEST',
            'order_date' => now(),
            'status' => 'approved',
        ]);

        $this->poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $this->po->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_price' => 10,
            'total' => 1000,
            'received_quantity' => 0,
        ]);
    }

    public function test_authorized_user_can_receive_approved_po()
    {
        $response = $this->actingAs($this->user)->post(route('admin.procurement.receipts.store'), [
            'code' => 'GR-TEST-001',
            'purchase_order_id' => $this->po->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $this->poItem->id,
                    'received_quantity' => 40,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.procurement.receipts.index'));

        $this->assertDatabaseHas('goods_receipts', [
            'purchase_order_id' => $this->po->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $this->poItem->id,
            'received_quantity' => 40,
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $this->po->id,
            'status' => 'partially_received',
        ]);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'available_quantity' => 40,
        ]);
    }

    public function test_cannot_receive_more_than_remaining_quantity()
    {
        $response = $this->actingAs($this->user)->post(route('admin.procurement.receipts.store'), [
            'code' => 'GR-TEST-002',
            'purchase_order_id' => $this->po->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $this->poItem->id,
                    'received_quantity' => 110,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $this->poItem->id,
            'received_quantity' => 0,
        ]);
    }

    public function test_company_isolation_cannot_receive_into_other_company_warehouse()
    {
        $otherCompany = Company::factory()->create();
        $otherWarehouse = Warehouse::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'company_id' => $otherCompany->id,
            'name' => 'Other Warehouse',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.procurement.receipts.store'), [
            'code' => 'GR-TEST-003',
            'purchase_order_id' => $this->po->id,
            'warehouse_id' => $otherWarehouse->id,
            'receipt_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $this->poItem->id,
                    'received_quantity' => 40,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('warehouse_id');
    }

    public function test_full_receiving_updates_status_to_completed()
    {
        $response = $this->actingAs($this->user)->post(route('admin.procurement.receipts.store'), [
            'code' => 'GR-TEST-004',
            'purchase_order_id' => $this->po->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now()->toDateString(),
            'items' => [
                [
                    'purchase_order_item_id' => $this->poItem->id,
                    'received_quantity' => 100,
                ]
            ]
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $this->po->id,
            'status' => 'completed',
        ]);
    }
}
