<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PurchaseRequisition;
use App\Models\SupplierQuotation;

class ProcurementPhase2ScenarioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_procurement_phase2_flow()
    {
        $this->withoutExceptionHandling();
        // 1. Setup User and Core Data
        $company = \App\Models\Company::factory()->create(['id' => 1]);
        
        $admin = User::factory()->create(['company_id' => 1]);
        $admin->assignRole('Super Admin');
        
        $warehouse = Warehouse::create([
            'company_id' => 1,
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'is_active' => true,
        ]);

        $product = Product::create([
            'company_id' => 1,
            'name' => 'Test Product',
            'code' => 'PRD-TEST',
            'sku' => 'SKU-12345',
            'type' => 'physical',
            'stock_quantity' => 10,
        ]);



        $supplierA = Supplier::create([
            'company_id' => 1,
            'name' => 'Supplier A',
            'code' => 'SUP-A',
            'email' => 'a@supp.com'
        ]);

        $supplierB = Supplier::create([
            'company_id' => 1,
            'name' => 'Supplier B',
            'code' => 'SUP-B',
            'email' => 'b@supp.com'
        ]);

        $requisition = PurchaseRequisition::create([
            'company_id' => 1,
            'code' => 'REQ-TEST-1',
            'request_date' => now(),
            'required_date' => now()->addDays(10),
            'status' => 'approved',
            'created_by' => $admin->id,
            'requested_by' => $admin->id,
        ]);
        
        $requisition->items()->create([
            'product_id' => $product->id,
            'quantity' => 50,
            'estimated_unit_price' => 10,
            'total_estimated_value' => 500,
        ]);

        $this->actingAs($admin);

        // 2. Submit Quotations for the Requisition
        $responseA = $this->post(route('admin.procurement.rfqs.store'), [
            'code' => 'QUO-A',
            'supplier_id' => $supplierA->id,
            'purchase_requisition_id' => $requisition->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(7)->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 50,
                    'unit_price' => 12, // More expensive
                    'discount' => 0,
                    'tax' => 0,
                ]
            ]
        ]);
        $responseA->assertRedirect();
        
        $responseB = $this->post(route('admin.procurement.rfqs.store'), [
            'code' => 'QUO-B',
            'supplier_id' => $supplierB->id,
            'purchase_requisition_id' => $requisition->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(7)->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 50,
                    'unit_price' => 9.5, // Cheaper
                    'discount' => 0,
                    'tax' => 0,
                ]
            ]
        ]);
        $responseB->assertRedirect();

        $quoB = SupplierQuotation::where('code', 'QUO-B')->first();

        // 3. Compare & Accept Quotation B
        $responseAccept = $this->post(route('admin.procurement.requisitions.accept', [$requisition->id, $quoB->id]));
        $responseAccept->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_quotation_id' => $quoB->id,
            'supplier_id' => $supplierB->id,
        ]);

        $po = \App\Models\PurchaseOrder::with('items')->where('supplier_quotation_id', $quoB->id)->first();
        if ($po->items->isEmpty()) {
            dd('PO ITEMS ARE EMPTY. Quotation items count: ' . $quoB->items()->count());
        }
        
        // 4. Approve Purchase Order
        $responseApprovePO = $this->post(route('admin.procurement.pos.approve', $po->id));
        $responseApprovePO->assertRedirect();
        
        $po->refresh();
        $this->assertEquals('approved', $po->status);

        // 5. Create Goods Receipt (Partial)
        $poItem = $po->items->first();
        $responseGR = $this->post(route('admin.procurement.receipts.store'), [
            'purchase_order_id' => $po->id,
            'warehouse_id' => $warehouse->id,
            'receipt_date' => now()->toDateString(),
            'delivery_note_number' => 'DN-001',
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'received_quantity' => 45, // 5 missing/damaged
                    'rejected_quantity' => 5,
                ]
            ]
        ]);
        $responseGR->assertRedirect();

        // 6. Verify Inventory updated
        $inventory = \App\Models\Inventory::where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->first();
        $this->assertEquals(45, $inventory->available_quantity);
    }
}
