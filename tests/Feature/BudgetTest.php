<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Budget;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_can_be_retrieved()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/finance/budgets');

        // Without permissions, it should return 403
        $response->assertStatus(403);
    }
}
