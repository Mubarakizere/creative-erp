<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Sequence;
use App\Models\User;
use App\Services\SequenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SequenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SequenceService::class);
    }

    public function test_generates_default_sequence_first_time(): void
    {
        $company = Company::factory()->create();
        
        $number = $this->service->generate('purchase_order', $company->id);
        
        $this->assertEquals('PO-000001', $number);
        
        $this->assertDatabaseHas('sequences', [
            'company_id' => $company->id,
            'document_type' => 'purchase_order',
            'next_number' => 2,
        ]);
    }

    public function test_increments_existing_sequence(): void
    {
        $company = Company::factory()->create();
        
        Sequence::create([
            'company_id' => $company->id,
            'document_type' => 'invoice',
            'prefix' => 'INV-2023-',
            'padding' => 4,
            'next_number' => 150,
            'active' => true,
        ]);
        
        $number = $this->service->generate('invoice', $company->id);
        
        $this->assertEquals('INV-2023-0150', $number);
        
        $this->assertDatabaseHas('sequences', [
            'company_id' => $company->id,
            'document_type' => 'invoice',
            'next_number' => 151,
        ]);
    }

    public function test_uses_auth_company_if_not_provided(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($user);
        
        $number = $this->service->generate('quotation');
        
        $this->assertEquals('QT-000001', $number);
        
        $this->assertDatabaseHas('sequences', [
            'company_id' => $company->id,
            'document_type' => 'quotation',
        ]);
    }

    public function test_generates_fallback_for_unknown_type(): void
    {
        $company = Company::factory()->create();
        
        $number = $this->service->generate('custom_doc', $company->id);
        
        $this->assertEquals('CUSTOM_DOC-000001', $number);
    }
}
