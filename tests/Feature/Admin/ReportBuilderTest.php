<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\ReportTemplate;
use Spatie\Permission\Models\Role;

class ReportBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['name' => 'Creative Engineering Co']);
        $this->branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);
    }

    public function test_user_can_access_report_builder_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.builder'));

        $response->assertStatus(200);
        $response->assertSee('Report Studio Builder');
        $response->assertSee('Source & Info', false);
        $response->assertSee('Filters');
        $response->assertSee('Columns');
        $response->assertSee('Chart & Sort', false);
        $response->assertSee('Live Preview Canvas');
        $response->assertSee('Data Source (Module)', false);
    }

    public function test_user_can_preview_project_summary_report(): void
    {
        Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Kigali Tower Project',
            'status' => 'In Progress',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.reports.preview'), [
            'type' => 'project_summary',
            'filters' => [
                'company_id' => $this->company->id,
            ],
            'layout' => [
                'columns' => ['id', 'name', 'status'],
                'chartType' => 'table',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Detailed Dataset');
        $response->assertSee('Kigali Tower Project');
    }

    public function test_user_can_preview_invoice_summary_report(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.reports.preview'), [
            'type' => 'invoice_summary',
            'filters' => [],
            'layout' => [
                'columns' => ['id', 'invoice_number', 'total_amount'],
                'chartType' => 'bar',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Detailed Dataset');
    }

    public function test_user_can_preview_inventory_stock_report(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.reports.preview'), [
            'type' => 'stock_on_hand',
            'filters' => [],
            'layout' => [
                'columns' => ['id', 'item_code', 'name', 'quantity_on_hand'],
                'chartType' => 'doughnut',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee('Detailed Dataset');
    }

    public function test_user_can_store_custom_report_template(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.reports.store'), [
            'name' => 'Executive Project Health Report',
            'description' => 'Aggregated view for quarterly management board review',
            'type' => 'project_summary',
            'filters' => [
                'company_id' => $this->company->id,
                'status' => 'In Progress',
            ],
            'layout' => [
                'columns' => ['id', 'name', 'status', 'progress'],
                'groupBy' => 'status',
                'sortBy' => 'created_at',
                'sortDirection' => 'desc',
                'chartType' => 'bar',
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('report_templates', [
            'name' => 'Executive Project Health Report',
            'type' => 'project_summary',
            'created_by' => $this->user->id,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_user_can_edit_and_update_report_template(): void
    {
        $template = ReportTemplate::create([
            'name' => 'Initial Report Draft',
            'description' => 'Draft description',
            'type' => 'project_summary',
            'filters' => ['status' => 'Pending'],
            'layout' => ['columns' => ['id', 'name'], 'chartType' => 'table'],
            'created_by' => $this->user->id,
            'company_id' => $this->company->id,
            'is_system' => false,
        ]);

        // Load builder in edit mode
        $editResponse = $this->actingAs($this->user)->get(route('admin.reports.builder', ['template_id' => $template->id]));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Initial Report Draft');

        // Update template
        $updateResponse = $this->actingAs($this->user)->put(route('admin.reports.update', $template), [
            'name' => 'Final Approved Report',
            'description' => 'Updated description',
            'filters' => ['status' => 'Completed'],
            'layout' => ['columns' => ['id', 'name', 'status'], 'chartType' => 'line'],
        ]);

        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('report_templates', [
            'id' => $template->id,
            'name' => 'Final Approved Report',
        ]);
    }
}
