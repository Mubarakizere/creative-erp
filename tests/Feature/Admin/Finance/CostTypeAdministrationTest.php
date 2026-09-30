<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\CostType;
use App\Models\Project;
use App\Models\Budget;
use App\Models\BudgetLine;
use Spatie\Permission\Models\Role;

class CostTypeAdministrationTest extends TestCase
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

        // Ensure default core system cost types exist
        CostType::firstOrCreate(['slug' => 'labor'], [
            'name' => 'Labor',
            'is_system' => true,
            'is_active' => true,
        ]);
        CostType::firstOrCreate(['slug' => 'materials'], [
            'name' => 'Materials',
            'is_system' => true,
            'is_active' => true,
        ]);
        CostType::firstOrCreate(['slug' => 'other'], [
            'name' => 'Other',
            'is_system' => true,
            'is_active' => true,
        ]);
    }

    public function test_cost_types_are_displayed_in_finance_settings()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->get(route('admin.finance.settings'));

        $response->assertStatus(200);
        $response->assertSee('Project Budget Cost Types');
        $response->assertSee('Labor');
        $response->assertSee('Materials');
        $response->assertSee('Other');
    }

    public function test_can_create_custom_cost_type_via_settings()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->post(route('admin.finance.settings.cost-types.store'), [
                'name' => 'Subcontractor Civil Works',
                'description' => 'External masonry and foundation teams',
            ]);

        $response->assertRedirect(route('admin.finance.settings'));
        $this->assertDatabaseHas('cost_types', [
            'name' => 'Subcontractor Civil Works',
            'slug' => 'subcontractor_civil_works',
            'is_system' => false,
        ]);
    }

    public function test_can_create_custom_cost_type_via_ajax()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->postJson(route('admin.finance.settings.cost-types.store'), [
                'name' => 'Heavy Equipment & Machinery',
                'description' => 'Excavators, mixers and site cranes',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cost_type' => [
                'name' => 'Heavy Equipment & Machinery',
                'slug' => 'heavy_equipment_machinery',
            ],
        ]);

        $this->assertDatabaseHas('cost_types', [
            'slug' => 'heavy_equipment_machinery',
        ]);
    }

    public function test_can_update_cost_type()
    {
        $ct = CostType::create([
            'company_id' => $this->company->id,
            'name' => 'Old Equipment Label',
            'slug' => 'old_equipment_label',
            'description' => 'Old description',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->put(route('admin.finance.settings.cost-types.update', $ct->id), [
                'name' => 'Updated Equipment Label',
                'description' => 'Updated notes on machinery',
            ]);

        $response->assertRedirect(route('admin.finance.settings'));
        $ct->refresh();
        $this->assertEquals('Updated Equipment Label', $ct->name);
        $this->assertEquals('Updated notes on machinery', $ct->description);
    }

    public function test_cannot_delete_system_cost_types()
    {
        $labor = CostType::where('slug', 'labor')->first();

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->delete(route('admin.finance.settings.cost-types.destroy', $labor->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('cost_types', [
            'slug' => 'labor',
            'deleted_at' => null,
        ]);
    }

    public function test_cannot_delete_cost_type_assigned_to_budget_lines()
    {
        $ct = CostType::create([
            'company_id' => $this->company->id,
            'name' => 'Permits & Approvals',
            'slug' => 'permits_approvals',
            'is_system' => false,
        ]);

        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $budget = Budget::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'name' => 'Commercial Tower Budget',
            'total_amount' => 500000,
            'status' => 'draft',
        ]);

        BudgetLine::create([
            'budget_id' => $budget->id,
            'project_id' => $project->id,
            'cost_type' => 'permits_approvals',
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->delete(route('admin.finance.settings.cost-types.destroy', $ct->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('cost_types', [
            'id' => $ct->id,
            'deleted_at' => null,
        ]);
    }

    public function test_can_delete_unassigned_custom_cost_type()
    {
        $ct = CostType::create([
            'company_id' => $this->company->id,
            'name' => 'Temporary Classification',
            'slug' => 'temporary_classification',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->delete(route('admin.finance.settings.cost-types.destroy', $ct->id));

        $response->assertRedirect(route('admin.finance.settings'));
        $this->assertSoftDeleted('cost_types', ['id' => $ct->id]);
    }

    public function test_budget_create_page_displays_all_active_cost_types()
    {
        CostType::create([
            'company_id' => $this->company->id,
            'name' => 'Architectural Consulting',
            'slug' => 'architectural_consulting',
            'is_system' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['company_id' => $this->company->id])
            ->get(route('admin.finance.budgets.create'));

        $response->assertStatus(200);
        $response->assertSee('Architectural Consulting');
        $response->assertSee('quick-add-cost-type');
        $response->assertSee('quick-add-budget-category');
    }
}
