<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseRequisition;

class ProcurementScenarioTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        $permissions = [
            'supplier.create', 'supplier.view', 'supplier.update',
            'procurement.create', 'procurement.view', 'procurement.approve',
            'purchase_order.create', 'purchase_order.view', 'purchase_order.approve',
            'goods_receipt.create',
            'supplier_payment.create', 'supplier_payment.view'
        ];
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        
        foreach ($permissions as $p) {
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            $role->givePermissionTo($permission);
        }
        $this->user->assignRole($role);
    }

    public function test_procurement_scenario()
    {
        // 1. Supplier Management
        $response = $this->actingAs($this->user)->post(route('admin.procurement.suppliers.store'), [
            'name' => 'Global Tech Supplies',
            'code' => 'SUP-001',
            'email' => 'contact@globaltech.com',
            'is_preferred' => 1,
        ]);
        $response->assertRedirect(route('admin.procurement.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['code' => 'SUP-001', 'is_preferred' => 1]);

        $supplier = Supplier::where('code', 'SUP-001')->first();

        // 2. Purchase Requisition
        $product1 = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Server Rack',
            'code' => 'PRD-SRV-01',
            'sku' => 'SKU-001',
            'type' => 'goods',
            'cost_price' => 500,
            'selling_price' => 700,
            'created_by' => $this->user->id,
        ]);

        $product2 = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Network Switch',
            'code' => 'PRD-NET-01',
            'sku' => 'SKU-002',
            'type' => 'goods',
            'cost_price' => 200,
            'selling_price' => 300,
            'created_by' => $this->user->id,
        ]);

        // Submit as draft
        $response = $this->actingAs($this->user)->post(route('admin.procurement.requisitions.store'), [
            'code' => 'REQ-2023-001',
            'status' => 'draft',
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 5],
            ]
        ]);
        $response->assertRedirect(route('admin.procurement.requisitions.index'));
        $this->assertDatabaseHas('purchase_requisitions', ['code' => 'REQ-2023-001', 'status' => 'draft']);

        $requisition = PurchaseRequisition::where('code', 'REQ-2023-001')->first();

        // Cannot approve own requisition
        $response = $this->actingAs($this->user)->post(route('admin.procurement.requisitions.approve', $requisition->id));
        $response->assertSessionHas('error'); // Cannot approve own

        // Update status directly to simulate another manager approving it
        $requisition->update(['status' => 'approved']);

        // 3. Request for Quotation (RFQ)
        $response = $this->actingAs($this->user)->post(route('admin.procurement.rfqs.store'), [
            'code' => 'RFQ-2023-001',
            'supplier_id' => $supplier->id,
            'purchase_requisition_id' => $requisition->id,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(7)->toDateString(),
        ]);
        
        $response->assertRedirect(route('admin.procurement.rfqs.index'));
        $this->assertDatabaseHas('supplier_quotations', [
            'code' => 'RFQ-2023-001', 
            'supplier_id' => $supplier->id,
            'status' => 'draft'
        ]);
    }
}
