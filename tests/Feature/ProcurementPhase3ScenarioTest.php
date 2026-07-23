<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;

class ProcurementPhase3ScenarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_procurement_phase3_flow()
    {
        $this->withoutExceptionHandling();
        
        // 1. Setup Data
        $company = \App\Models\Company::factory()->create(['id' => 1]);
        $user = User::factory()->create(['company_id' => 1]);
        
        // Ensure role and permission checks pass
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'company_id' => 1]);
        $permission1 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'procurement.create', 'guard_name' => 'web']);
        $permission2 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'supplier_payment.create', 'guard_name' => 'web']);
        $role->givePermissionTo($permission1);
        $role->givePermissionTo($permission2);
        $user->assignRole($role);

        $product = Product::create([
            'company_id' => 1,
            'name' => 'Test Product',
            'code' => 'PRD-TEST',
            'sku' => 'SKU-12345',
            'type' => 'physical',
            'stock_quantity' => 10,
        ]);

        $supplier = Supplier::create([
            'company_id' => 1,
            'name' => 'Supplier X',
            'code' => 'SUP-X',
            'email' => 'supplierX@test.com',
        ]);

        $liabilityType = \App\Models\AccountType::create(['company_id' => 1, 'name' => 'Liability', 'code' => 'LIA', 'category' => 'liability']);
        $assetType = \App\Models\AccountType::create(['company_id' => 1, 'name' => 'Asset', 'code' => 'ASS', 'category' => 'asset']);

        \App\Models\ChartOfAccount::create(['company_id' => 1, 'account_type_id' => $liabilityType->id, 'code' => '2100', 'name' => 'GRNI', 'is_active' => true]);
        \App\Models\ChartOfAccount::create(['company_id' => 1, 'account_type_id' => $liabilityType->id, 'code' => '2000', 'name' => 'Accounts Payable', 'is_active' => true]);
        \App\Models\ChartOfAccount::create(['company_id' => 1, 'account_type_id' => $assetType->id, 'code' => '1000', 'name' => 'Cash', 'is_active' => true]);

        // Mock an approved Purchase Order
        $po = PurchaseOrder::create([
            'company_id' => 1,
            'code' => 'PO-1234',
            'supplier_id' => $supplier->id,
            'status' => 'approved',
            'order_date' => now(),
            'delivery_date' => now()->addDays(5),
            'created_by' => $user->id,
        ]);

        $poItem = $po->items()->create([
            'product_id' => $product->id,
            'quantity' => 100,
            'unit_price' => 20,
            'discount' => 0,
            'tax' => 10,
            'total' => 2010, // 100*20 + 10
        ]);

        $this->actingAs($user);

        // 2. Create Purchase Invoice against PO
        $responseInvoice = $this->post(route('admin.procurement.invoices.store'), [
            'invoice_number' => 'INV-001',
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 2000,
            'tax_amount' => 10,
            'discount_amount' => 0,
            'grand_total' => 2010,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 100,
                    'unit_price' => 20,
                    'tax' => 10,
                    'discount' => 0,
                    'total' => 2010,
                    'purchase_order_item_id' => $poItem->id,
                ]
            ]
        ]);

        $responseInvoice->assertRedirect();
        
        $invoice = PurchaseInvoice::where('invoice_number', 'INV-001')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(2010, $invoice->grand_total);
        $this->assertEquals('draft', $invoice->status); // Initial status
        
        // PO should be marked completed
        $po->refresh();
        $this->assertEquals('completed', $po->status);

        // 3. Make Partial Payment
        $responsePartialPayment = $this->post(route('admin.procurement.payments.store'), [
            'payment_number' => 'PAY-001',
            'supplier_id' => $supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => 1000,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'REF-BANK-001',
        ]);

        $responsePartialPayment->assertRedirect();
        
        $invoice->refresh();
        $this->assertEquals(1000, $invoice->paid_amount);
        $this->assertEquals('partially_paid', $invoice->status);

        // 4. Make Remaining Payment
        $responseFullPayment = $this->post(route('admin.procurement.payments.store'), [
            'payment_number' => 'PAY-002',
            'supplier_id' => $supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => 1010,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'REF-BANK-002',
        ]);

        $responseFullPayment->assertRedirect();
        
        $invoice->refresh();
        $this->assertEquals(2010, $invoice->paid_amount);
        $this->assertEquals('paid', $invoice->status);

        // Verify journal entries got created (Count should be at least 3, Invoice + 2 Payments)
        $journalsCount = \App\Models\Journal::where('company_id', 1)->count();
        $this->assertGreaterThanOrEqual(3, $journalsCount);
    }
}
