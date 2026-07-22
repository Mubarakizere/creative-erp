<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Budget;
use Spatie\Permission\Models\Role;

class FinancialSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_budget_with_permission()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $role = Role::create(['name' => 'Finance']);
        $role->givePermissionTo('budget.view');
        $user->assignRole($role);
        
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/finance/budgets');

        $response->assertStatus(200);
    }
}
