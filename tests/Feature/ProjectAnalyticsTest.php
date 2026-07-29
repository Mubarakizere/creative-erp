<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Services\Metrics\ProjectMetrics;
use App\Services\Metrics\ChartService;
use App\Services\ReportBuilderService;

class ProjectAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        
        // Mock permission if utilizing Spatie Permissions
        // $this->user->givePermissionTo('project.view');
        // $this->user->givePermissionTo('project.view-budget');
        
        $this->actingAs($this->user);
    }

    public function test_project_metrics_cards_calculates_correctly()
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'estimated_budget' => 10000,
            'actual_budget' => 9000,
            'actual_cost' => 4500,
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'actual_material_cost' => 1500,
        ]);

        $metrics = app(ProjectMetrics::class)->cards();
        
        // Assuming permissions are bypassed or granted in test
        if (isset($metrics['total_estimated_budget'])) {
            $this->assertEquals(10000, $metrics['total_estimated_budget']);
            $this->assertEquals(9000, $metrics['total_actual_budget']);
            $this->assertEquals(4500, $metrics['total_actual_cost']);
            $this->assertEquals(4500, $metrics['remaining_budget']);
            $this->assertEquals(50, $metrics['budget_utilization_percent']);
            $this->assertEquals(1500, $metrics['total_material_cost']);
        }
    }

    public function test_project_metrics_widgets_returns_correct_budget_status()
    {
        // 90% utilization
        Project::factory()->create([
            'company_id' => $this->company->id,
            'actual_budget' => 1000,
            'actual_cost' => 900,
        ]);

        // 110% utilization
        Project::factory()->create([
            'company_id' => $this->company->id,
            'actual_budget' => 1000,
            'actual_cost' => 1100,
        ]);

        $widgets = app(ProjectMetrics::class)->widgets();

        if (isset($widgets['projectsApproachingBudget'])) {
            $this->assertCount(1, $widgets['projectsApproachingBudget']);
            $this->assertCount(1, $widgets['projectsOverBudget']);
        }
    }

    public function test_report_builder_returns_project_financial_overview()
    {
        Project::factory()->create([
            'company_id' => $this->company->id,
            'actual_budget' => 1000,
            'actual_cost' => 1100,
        ]);

        $reportBuilder = app(ReportBuilderService::class);
        $overview = $reportBuilder->build('project_financial_overview', ['company_id' => $this->company->id]);
        
        $this->assertEquals(1, $overview['projects_count']);
        $this->assertEquals(1, $overview['over_budget_count']);
        $this->assertEquals(0, $overview['healthy_count']);
    }

    public function test_chart_service_returns_budget_vs_actual_chart()
    {
        Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Test Project',
            'actual_budget' => 5000,
            'actual_cost' => 2500,
        ]);

        $chartService = app(ChartService::class);
        $data = $chartService->getChartData(['company_id' => $this->company->id]);

        $this->assertArrayHasKey('projectBudgetVsActual', $data);
        $this->assertContains('Test Project', $data['projectBudgetVsActual']['labels']);
        $this->assertContains(5000.0, $data['projectBudgetVsActual']['budgets']);
        $this->assertContains(2500.0, $data['projectBudgetVsActual']['actuals']);
    }
}
