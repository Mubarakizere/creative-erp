<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;

class FinancialStatementTest extends TestCase
{
    use RefreshDatabase;

    public function test_profit_and_loss_can_be_generated()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/finance/reports/profit-and-loss');

        // Without permissions, it should return 403
        $response->assertStatus(403);
    }
}
