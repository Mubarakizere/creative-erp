<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\BudgetCategory;
use App\Models\Project;
use App\Models\Budget;
use App\Models\BudgetLine;
use Spatie\Permission\Models\Role;

class BudgetCategoryAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['name' => 'Creative Century Engineering', 'status' => 'active']);
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);
    }

    public function test_budget_categories_are_displayed_in_finance_settings()
    {
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Heavy Equipment Hire',
            'type' => 'expense',
            'description' => 'Site excavators and cranes',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->get(route('admin.finance.settings'));

        $response->assertStatus(200);
        $response->assertSee('Project Cost');
        $response->assertSee('Budget Categories');
        $response->assertSee('Heavy Equipment Hire');
        $response->assertSee('Finance Settings');
        $response->assertSee(route('admin.finance.settings'));
    }

    public function test_can_create_cost_category_via_settings()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->post(route('admin.finance.settings.budget-categories.store'), [
                'name' => 'Electrical Cabling & Fixtures',
                'type' => 'expense',
                'description' => 'High voltage lines and fittings',
            ]);

        $response->assertRedirect(route('admin.finance.settings'));
        $this->assertDatabaseHas('budget_categories', [
            'company_id' => $this->company->id,
            'name' => 'Electrical Cabling & Fixtures',
            'type' => 'expense',
        ]);
    }

    public function test_can_update_cost_category()
    {
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Old Category Name',
            'type' => 'expense',
            'description' => 'Initial note',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->put(route('admin.finance.settings.budget-categories.update', $cat->id), [
                'name' => 'Updated Category Name',
                'type' => 'revenue',
                'description' => 'Updated note',
            ]);

        $response->assertRedirect(route('admin.finance.settings'));
        $cat->refresh();
        $this->assertEquals('Updated Category Name', $cat->name);
        $this->assertEquals('revenue', $cat->type);
    }

    public function test_can_delete_unassigned_cost_category()
    {
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Temporary Category',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->delete(route('admin.finance.settings.budget-categories.destroy', $cat->id));

        $response->assertRedirect(route('admin.finance.settings'));
        $this->assertSoftDeleted('budget_categories', ['id' => $cat->id]);
    }

    public function test_cannot_delete_category_with_assigned_budget_lines()
    {
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Critical Structural Steel',
            'type' => 'expense',
        ]);

        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $budget = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'name' => 'Alpha Tower Budget',
            'total_amount' => 100000,
            'status' => 'draft',
        ]);

        BudgetLine::create([
            'budget_id' => $budget->id,
            'project_id' => $project->id,
            'budget_category_id' => $cat->id,
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->delete(route('admin.finance.settings.budget-categories.destroy', $cat->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('budget_categories', [
            'id' => $cat->id,
            'deleted_at' => null,
        ]);
    }

    public function test_budget_create_page_shows_seeded_categories()
    {
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Civil Works & Excavation',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->get(route('admin.finance.budgets.create'));

        $response->assertStatus(200);
        $response->assertSee('Civil Works & Excavation');
        $response->assertSee(route('admin.finance.settings'));
    }

    public function test_can_create_budget_without_fiscal_year()
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $cat = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'General Construction',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->post(route('admin.finance.budgets.store'), [
                'project_id' => $project->id,
                'name' => 'EXNIHILO APARTMENT - Project Budget',
                'description' => 'Test project budget without FY',
                'fiscal_year_id' => null,
                'status' => 'active',
                'lines' => [
                    [
                        'activity_name' => 'Foundations',
                        'budget_category_id' => $cat->id,
                        'amount' => 44603500,
                    ]
                ],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'project_id' => $project->id,
            'name' => 'EXNIHILO APARTMENT - Project Budget',
            'total_amount' => 44603500,
        ]);
    }
}
