<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\FiscalYear;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\BudgetCategory;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BudgetCreationFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_budget_create_page_loads_successfully_without_sql_errors()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('Super Admin');

        FiscalYear::create([
            'company_id' => $company->id,
            'name' => 'FY 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_closed' => false,
        ]);

        $accountType = AccountType::create([
            'company_id' => $company->id,
            'name' => 'Operating Expense',
            'category' => 'Expense',
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'company_id' => $company->id,
            'account_type_id' => $accountType->id,
            'code' => '5001',
            'name' => 'Operational Expenses',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.finance.budgets.create'));

        $response->assertStatus(200);
        $response->assertSee('Operational Expenses');
    }

    public function test_budget_can_be_stored_successfully()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('Super Admin');

        $fiscalYear = FiscalYear::create([
            'company_id' => $company->id,
            'name' => 'FY 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_closed' => false,
        ]);

        $accountType = AccountType::create([
            'company_id' => $company->id,
            'name' => 'Operating Expense',
            'category' => 'Expense',
            'is_active' => true,
        ]);

        $account = ChartOfAccount::create([
            'company_id' => $company->id,
            'account_type_id' => $accountType->id,
            'code' => '5002',
            'name' => 'Marketing Expenses',
            'is_active' => true,
        ]);

        $category = BudgetCategory::create([
            'company_id' => $company->id,
            'name' => 'Marketing Operations',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.finance.budgets.store'), [
            'name' => 'Q3 Marketing Budget',
            'fiscal_year_id' => $fiscalYear->id,
            'description' => 'Marketing expenses for Q3',
            'lines' => [
                [
                    'budget_category_id' => $category->id,
                    'chart_of_account_id' => $account->id,
                    'amount' => 50000,
                    'notes' => 'Campaign budget',
                ]
            ]
        ]);

        $budget = Budget::where('name', 'Q3 Marketing Budget')->first();
        $this->assertNotNull($budget);
        $response->assertRedirect(route('admin.finance.budgets.show', $budget));
        $this->assertDatabaseHas('budgets', [
            'name' => 'Q3 Marketing Budget',
            'fiscal_year_id' => $fiscalYear->id,
            'total_amount' => 50000,
        ]);
    }
}
