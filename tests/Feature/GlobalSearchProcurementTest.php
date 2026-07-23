<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\SupplierQuotation;
use App\Models\GoodsReceipt;
use App\Models\PurchaseInvoice;

class GlobalSearchProcurementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock permission check for the test
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_global_search_returns_procurement_models()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'company_id' => $company->id]);
        
        $permissions = [
            'procurement.view',
            'supplier.view',
            'purchase_order.view',
            'quotation.view',
            'receipt.view',
            'invoice.view'
        ];
        
        foreach ($permissions as $perm) {
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            $role->givePermissionTo($permission);
        }
        
        $user->assignRole($role);
        
        $this->actingAs($user);

        // Create procurement items with specific codes to search for
        $supplier = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Acme Corporation',
            'code' => 'SUP-ACME',
            'email' => 'contact@acme.com'
        ]);

        $po = PurchaseOrder::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'code' => 'PO-ACME-001',
            'order_date' => now(),
            'status' => 'draft',
            'grand_total' => 1000
        ]);

        $rfq = SupplierQuotation::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'code' => 'RFQ-ACME-001',
            'status' => 'draft'
        ]);

        $gr = GoodsReceipt::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po->id,
            'code' => 'GR-ACME-001',
            'receipt_date' => now(),
            'status' => 'received',
        ]);

        $invoice = PurchaseInvoice::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po->id,
            'invoice_number' => 'INV-ACME-001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'grand_total' => 1000,
            'paid_amount' => 0,
        ]);

        // Search for ACME
        $response = $this->getJson(route('admin.search') . '?q=ACME');
        $response->assertStatus(200);

        $data = $response->json();
        
        $this->assertCount(1, $data['suppliers']);
        $this->assertEquals('Code: SUP-ACME', $data['suppliers'][0]['subtitle']);

        $this->assertCount(1, $data['purchase_orders']);
        $this->assertEquals('Purchase Order: PO-ACME-001', $data['purchase_orders'][0]['title']);

        $this->assertCount(1, $data['rfqs']);
        $this->assertEquals('RFQ: RFQ-ACME-001', $data['rfqs'][0]['title']);

        $this->assertCount(1, $data['goods_receipts']);
        $this->assertEquals('Goods Receipt: GR-ACME-001', $data['goods_receipts'][0]['title']);

        $this->assertCount(1, $data['purchase_invoices']);
        $this->assertEquals('Purchase Invoice: INV-ACME-001', $data['purchase_invoices'][0]['title']);
    }
}
