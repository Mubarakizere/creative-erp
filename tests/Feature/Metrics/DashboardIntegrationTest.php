<?php

namespace Tests\Feature\Metrics;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure required roles/permissions if using Spatie or custom
        // We'll just create a company and user, then login
        $company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $company->id,
        ]);
        $this->user->assignRole('Super Admin');
    }

    public function test_dashboard_integration_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard.index');
        
        // Assert that the view has the expected data structure passed from DashboardService -> MetricsService
        $response->assertViewHasAll([
            'stats',
            'chartData',
        ]);
        
        // Ensure specific keys exist in the returned view data
        $viewData = $response->original->getData();
        $this->assertArrayHasKey('stats', $viewData);
        $this->assertArrayHasKey('chartData', $viewData);
        
        // Check for specific stats that should be populated by MetricsService providers
        $this->assertArrayHasKey('companies', $viewData['stats']);
        $this->assertArrayHasKey('projects', $viewData['stats']);
        $this->assertArrayHasKey('users', $viewData['stats']);
    }
}
