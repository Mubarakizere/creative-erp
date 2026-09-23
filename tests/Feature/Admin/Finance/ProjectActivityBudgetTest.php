<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\Task;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\BudgetCategory;
use App\Models\FiscalYear;
use Spatie\Permission\Models\Role;

class ProjectActivityBudgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $projectAlpha;
    protected Project $projectBeta;
    protected Task $taskFoundation;
    protected Task $taskFraming;
    protected BudgetCategory $catMaterials;
    protected BudgetCategory $catLabor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['name' => 'Apex Construction Co', 'status' => 'active']);
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);

        $this->projectAlpha = Project::factory()->create([
            'company_id' => $this->company->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Alpha Tower Project',
            'project_code' => 'PRJ-ALPHA',
            'status' => 'In Progress',
            'estimated_budget' => 0,
        ]);

        $this->projectBeta = Project::factory()->create([
            'company_id' => $this->company->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Beta Bridge Project',
            'project_code' => 'PRJ-BETA',
            'status' => 'In Progress',
            'estimated_budget' => 0,
        ]);

        $this->taskFoundation = Task::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'task_code' => 'TSK-001',
            'name' => 'Foundation Excavation & Pouring',
            'status' => 'In Progress',
            'actual_material_cost' => 12000,
        ]);

        $this->taskFraming = Task::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'task_code' => 'TSK-002',
            'name' => 'Structural Steel Framing',
            'status' => 'Pending',
            'actual_material_cost' => 0,
        ]);

        $this->catMaterials = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Direct Materials',
            'type' => 'expense',
        ]);

        $this->catLabor = BudgetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Labor & Workforce',
            'type' => 'expense',
        ]);
    }

    public function test_user_can_view_budget_create_page_with_projects_and_activities()
    {
        $response = $this->actingAs($this->user)->get(route('admin.finance.budgets.create'));

        $response->assertOk();
        $response->assertViewHas('projects');
        $response->assertViewHas('projectsData');
        $response->assertViewHas('categories');

        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Direct Materials');
        $response->assertSee('Labor & Workforce');
    }

    public function test_user_can_create_project_activity_budget_allocating_money_per_task()
    {
        $payload = [
            'project_id' => $this->projectAlpha->id,
            'name' => 'Alpha Tower Q4 Execution Budget',
            'description' => 'Initial phase budget for excavation and framing.',
            'status' => 'active',
            'lines' => [
                [
                    'task_id' => $this->taskFoundation->id,
                    'budget_category_id' => $this->catMaterials->id,
                    'amount' => 30000,
                    'notes' => 'Concrete and rebar procurement',
                ],
                [
                    'task_id' => $this->taskFraming->id,
                    'budget_category_id' => $this->catLabor->id,
                    'amount' => 45000,
                    'notes' => 'Welding and assembly contractor',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.finance.budgets.store'), $payload);

        $budget = Budget::where('name', 'Alpha Tower Q4 Execution Budget')->first();
        $this->assertNotNull($budget);

        $response->assertRedirect(route('admin.finance.budgets.show', $budget));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'project_id' => $this->projectAlpha->id,
            'company_id' => $this->company->id,
            'name' => 'Alpha Tower Q4 Execution Budget',
            'total_amount' => 75000,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('budget_lines', [
            'budget_id' => $budget->id,
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFoundation->id,
            'amount' => 30000,
            'notes' => 'Concrete and rebar procurement',
        ]);

        $this->assertDatabaseHas('budget_lines', [
            'budget_id' => $budget->id,
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFraming->id,
            'amount' => 45000,
            'notes' => 'Welding and assembly contractor',
        ]);

        // Verify project estimated_budget auto-synced
        $this->projectAlpha->refresh();
        $this->assertEquals(75000, $this->projectAlpha->estimated_budget);
    }

    public function test_budget_index_displays_project_and_filters_by_project()
    {
        $budgetAlpha = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Budget Alpha High Rise',
            'total_amount' => 50000,
            'status' => 'active',
        ]);
        $budgetAlpha->lines()->create([
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFoundation->id,
            'amount' => 50000,
        ]);

        $budgetBeta = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectBeta->id,
            'name' => 'Budget Beta Suspension',
            'total_amount' => 80000,
            'status' => 'draft',
        ]);

        // General index view
        $response = $this->actingAs($this->user)->get(route('admin.finance.budgets.index'));
        $response->assertOk();
        $response->assertSee('Budget Alpha High Rise');
        $response->assertSee('Budget Beta Suspension');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Beta Bridge Project');
        $response->assertSee('PRJ-BETA');

        // Filter by project Alpha
        $responseFilter = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'project_id' => $this->projectAlpha->id
        ]));
        $responseFilter->assertOk();
        $responseFilter->assertSee('Budget Alpha High Rise');
        $responseFilter->assertDontSee('Budget Beta Suspension');
    }

    public function test_budget_show_displays_activity_breakdown_and_actual_variance()
    {
        $budget = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Alpha Tower Performance Budget',
            'total_amount' => 30000,
            'status' => 'active',
        ]);

        // Task foundation has actual_material_cost of 12000
        $budget->lines()->create([
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFoundation->id,
            'amount' => 30000,
            'notes' => 'Excavation cost envelope',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.budgets.show', $budget));

        $response->assertOk();
        $response->assertSee('Alpha Tower Performance Budget');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('TSK-001');
        $response->assertSee('Foundation Excavation');
    }

    public function test_user_can_update_project_activity_budget_lines()
    {
        $budget = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Alpha Tower Initial Draft',
            'total_amount' => 30000,
            'status' => 'draft',
        ]);

        $budget->lines()->create([
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFoundation->id,
            'amount' => 30000,
        ]);

        $editResponse = $this->actingAs($this->user)->get(route('admin.finance.budgets.edit', $budget));
        $editResponse->assertOk();
        $editResponse->assertSee('Alpha Tower Initial Draft');

        $updatePayload = [
            'name' => 'Alpha Tower Revised Budget',
            'status' => 'approved',
            'lines' => [
                [
                    'task_id' => $this->taskFoundation->id,
                    'amount' => 35000,
                    'notes' => 'Expanded excavation depth',
                ],
                [
                    'task_id' => $this->taskFraming->id,
                    'amount' => 50000,
                    'notes' => 'Additional reinforcement',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->put(route('admin.finance.budgets.update', $budget), $updatePayload);
        $response->assertRedirect(route('admin.finance.budgets.show', $budget));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'name' => 'Alpha Tower Revised Budget',
            'status' => 'approved',
            'total_amount' => 85000,
        ]);

        $this->assertDatabaseHas('budget_lines', [
            'budget_id' => $budget->id,
            'task_id' => $this->taskFoundation->id,
            'amount' => 35000,
        ]);

        $this->assertDatabaseHas('budget_lines', [
            'budget_id' => $budget->id,
            'task_id' => $this->taskFraming->id,
            'amount' => 50000,
        ]);

        // Verify project estimated_budget auto-synced
        $this->projectAlpha->refresh();
        $this->assertEquals(85000, $this->projectAlpha->estimated_budget);
    }

    public function test_budget_index_displays_kpi_summary_cards_and_status_tabs()
    {
        Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Active Budget 1',
            'total_amount' => 50000,
            'status' => 'active',
        ]);

        Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectBeta->id,
            'name' => 'Approved Budget 2',
            'total_amount' => 30000,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.budgets.index'));

        $response->assertOk();
        $response->assertSee('Total Budgets');
        $response->assertSee('Total Allocated Capital');
        $response->assertSee('Active &amp; Approved', false);
        $response->assertSee('Funded Activities');
        $response->assertSee('Export CSV');

        // Test status tab filter
        $responseActive = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', ['status' => 'active']));
        $responseActive->assertOk();
        $responseActive->assertSee('Active Budget 1');
        $responseActive->assertDontSee('Approved Budget 2');
    }

    public function test_budget_index_supports_deep_search_by_activity_name_and_project_code()
    {
        $budget1 = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Alpha Excavation Phase',
            'total_amount' => 40000,
            'status' => 'active',
        ]);
        $budget1->lines()->create([
            'project_id' => $this->projectAlpha->id,
            'task_id' => $this->taskFoundation->id, // Foundation Excavation & Pouring
            'amount' => 40000,
        ]);

        $budget2 = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectBeta->id,
            'name' => 'Beta Steel Phase',
            'total_amount' => 60000,
            'status' => 'active',
        ]);
        $budget2->lines()->create([
            'project_id' => $this->projectBeta->id,
            'activity_name' => 'Suspension Cable Installation',
            'amount' => 60000,
        ]);

        // 1. Search by task name in lines
        $searchTask = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'search' => 'Foundation'
        ]));
        $searchTask->assertOk();
        $searchTask->assertSee('Alpha Excavation Phase');
        $searchTask->assertDontSee('Beta Steel Phase');

        // 2. Search by custom activity name in lines
        $searchActivity = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'search' => 'Suspension Cable'
        ]));
        $searchActivity->assertOk();
        $searchActivity->assertSee('Beta Steel Phase');
        $searchActivity->assertDontSee('Alpha Excavation Phase');

        // 3. Search by project code
        $searchCode = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'search' => 'PRJ-ALPHA'
        ]));
        $searchCode->assertOk();
        $searchCode->assertSee('Alpha Excavation Phase');
        $searchCode->assertDontSee('Beta Steel Phase');
    }

    public function test_budget_index_supports_amount_range_and_sorting_filters()
    {
        Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Small Maintenance Budget',
            'total_amount' => 5000,
            'status' => 'active',
        ]);

        Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectBeta->id,
            'name' => 'Mega Infrastructure Budget',
            'total_amount' => 500000,
            'status' => 'active',
        ]);

        // Filter by min amount = 10000
        $responseMin = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'min_amount' => 10000
        ]));
        $responseMin->assertOk();
        $responseMin->assertSee('Mega Infrastructure Budget');
        $responseMin->assertDontSee('Small Maintenance Budget');

        // Filter by max amount = 10000
        $responseMax = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'max_amount' => 10000
        ]));
        $responseMax->assertOk();
        $responseMax->assertSee('Small Maintenance Budget');
        $responseMax->assertDontSee('Mega Infrastructure Budget');

        // Sort by amount descending
        $responseSort = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'sort_by' => 'amount_desc'
        ]));
        $responseSort->assertOk();
        $responseSort->assertSeeInOrder(['Mega Infrastructure Budget', 'Small Maintenance Budget']);
    }

    public function test_budget_index_can_export_csv()
    {
        $budget = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $this->projectAlpha->id,
            'name' => 'Exportable Project Budget',
            'total_amount' => 75000,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.budgets.index', [
            'export' => 'csv'
        ]));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'project_budgets_'));
    }
}
