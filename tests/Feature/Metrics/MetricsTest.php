<?php

namespace Tests\Feature\Metrics;

use App\Services\Metrics\MetricsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $user = \App\Models\User::factory()->create(['status' => 'active']);
        $user->assignRole($role);
        $this->actingAs($user);
    }

    public function test_metrics_service_aggregates_cards()
    {
        $metricsService = app(MetricsService::class);
        $cards = $metricsService->cards();

        $this->assertIsArray($cards);
        $this->assertArrayHasKey('companies', $cards);
        $this->assertArrayHasKey('projects', $cards);
        $this->assertArrayHasKey('total_tasks', $cards);
    }

    public function test_metrics_service_caches_results()
    {
        $user = auth()->user();
        $cacheKey = "metrics_cards_" . $user->id . "_all";
        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn(['cached' => true]);

        $metricsService = app(MetricsService::class);
        $result = $metricsService->cards();

        $this->assertEquals(['cached' => true], $result);
    }

    public function test_metrics_service_get_cards_alias()
    {
        $metricsService = app(MetricsService::class);
        $cards = $metricsService->getCards();

        $this->assertIsArray($cards);
        $this->assertArrayHasKey('companies', $cards);
    }

    public function test_asset_metrics_cards()
    {
        $assetMetrics = app(\App\Services\Metrics\AssetMetrics::class);
        $cards = $assetMetrics->cards();

        $this->assertCount(4, $cards);
        $titles = array_column($cards, 'title');
        $this->assertContains('Total Assets', $titles);
        $this->assertContains('Net Book Value', $titles);
        $this->assertContains('Monthly Depreciation', $titles);
        $this->assertContains('Under Maintenance', $titles);
    }

    public function test_admin_assets_index_route_renders_successfully()
    {
        $response = $this->get(route('admin.assets.index'));
        $response->assertStatus(200);
        $response->assertSee('Fixed Assets');
        $response->assertSee('Total Assets');
        $response->assertSee('Net Book Value');
    }
}
