<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class FinancialMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_metrics_are_protected()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/dashboard/executive');

        // Without permissions, it should return 403
        $response->assertStatus(403);
    }
}
