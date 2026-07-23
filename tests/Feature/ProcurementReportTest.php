<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\PurchaseInvoice;
use App\Models\SupplierPayment;
use App\Services\ReportBuilderService;

class ProcurementReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable activity logging in tests if necessary or setup needed defaults
    }

    public function test_can_build_procurement_reports()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);

        // Create some data
        $supplier = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Test Supplier',
            'code' => 'TEST-SUP',
            'email' => 'sup@test.com'
        ]);
        
        $po = PurchaseOrder::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'status' => 'completed',
            'code' => 'PO-TEST',
            'order_date' => now(),
            'grand_total' => 1000
        ]);

        $receipt = GoodsReceipt::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po->id,
            'receipt_number' => 'GR-TEST',
            'code' => 'GR-TEST-C',
            'receipt_date' => now(),
            'status' => 'received',
        ]);

        $invoice = PurchaseInvoice::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $po->id,
            'invoice_number' => 'INV-TEST',
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'draft',
            'grand_total' => 1000,
            'paid_amount' => 0,
        ]);

        $payment = SupplierPayment::create([
            'company_id' => $company->id,
            'supplier_id' => $supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'payment_number' => 'PAY-TEST',
            'payment_method' => 'bank_transfer',
            'payment_date' => now(),
            'amount' => 500,
        ]);

        $builder = app(ReportBuilderService::class);
        $filters = ['company_id' => $company->id];

        // 1. Purchase Orders
        $pos = $builder->build('purchase_orders', $filters);
        $this->assertNotEmpty($pos);

        // 2. Supplier Spend
        $spend = $builder->build('supplier_spend', $filters);
        $this->assertNotEmpty($spend);
        $this->assertEquals(500, $spend->firstWhere('id', $supplier->id)->total_spend);

        // 3. Supplier Performance
        $perf = $builder->build('supplier_performance', $filters);
        $this->assertNotEmpty($perf);
        $this->assertEquals(1, $perf->firstWhere('id', $supplier->id)->completed_orders);

        // 4. Goods Receipts
        $receipts = $builder->build('goods_receipts', $filters);
        $this->assertNotEmpty($receipts);

        // 5. Purchase Invoices
        $invoices = $builder->build('purchase_invoices', $filters);
        $this->assertNotEmpty($invoices);

        // 6. Outstanding Payments
        $outstanding = $builder->build('outstanding_supplier_payments', $filters);
        $this->assertNotEmpty($outstanding);
        $this->assertEquals(1000, $outstanding->first()->outstanding_amount);

        // 7. Lead Time
        $leadTime = $builder->build('lead_time_report', $filters);
        $this->assertNotEmpty($leadTime);
        $this->assertEquals(1, $leadTime->first()->order_count);
    }
}
