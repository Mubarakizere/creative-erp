<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Quotation;
use App\Models\Tax;
use App\Models\Company;
use App\Services\QuotationService;

class QuotationCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected QuotationService $quotationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quotationService = new QuotationService();
    }

    public function test_quotation_calculates_totals_with_exclusive_tax()
    {
        $company = Company::factory()->create();
        
        $tax = Tax::create([
            'company_id' => $company->id,
            'name' => 'VAT 10%',
            'rate' => 10,
            'type' => 'exclusive'
        ]);

        $quotation = $this->quotationService->createQuotation([
            'company_id' => $company->id,
            'quotation_number' => 'QT-001'
        ], [
            [
                'product_name' => 'Service A',
                'quantity' => 2,
                'unit_price' => 100, // Gross 200
                'discount' => 20, // fixed discount, Net 180
                'discount_type' => 'fixed',
                'tax_id' => $tax->id // exclusive 10%, Tax 18, Total 198
            ],
            [
                'product_name' => 'Service B',
                'quantity' => 1,
                'unit_price' => 50, // Gross 50
                'discount' => 10, // 10% discount, Net 45
                'discount_type' => 'percentage',
                'tax_id' => null // Total 45
            ]
        ]);

        $this->assertEquals(225, $quotation->subtotal); // 180 + 45
        $this->assertEquals(25, $quotation->total_discount); // 20 + 5
        $this->assertEquals(18, $quotation->total_tax); // 18 + 0
        $this->assertEquals(243, $quotation->grand_total); // 198 + 45
    }

    public function test_quotation_calculates_totals_with_inclusive_tax()
    {
        $company = Company::factory()->create();
        
        $tax = Tax::create([
            'company_id' => $company->id,
            'name' => 'VAT 20%',
            'rate' => 20,
            'type' => 'inclusive'
        ]);

        $quotation = $this->quotationService->createQuotation([
            'company_id' => $company->id,
            'quotation_number' => 'QT-002'
        ], [
            [
                'product_name' => 'Product C',
                'quantity' => 1,
                'unit_price' => 120, // Net 120 inclusive of tax
                'discount' => 0,
                'discount_type' => 'fixed',
                'tax_id' => $tax->id
            ]
        ]);

        // Net before tax = 120
        // Total = 120
        // Tax = 120 - (120 / 1.2) = 120 - 100 = 20
        // Subtotal = 100
        
        $this->assertEquals(100, $quotation->subtotal);
        $this->assertEquals(0, $quotation->total_discount);
        $this->assertEquals(20, $quotation->total_tax);
        $this->assertEquals(120, $quotation->grand_total);
    }
}
