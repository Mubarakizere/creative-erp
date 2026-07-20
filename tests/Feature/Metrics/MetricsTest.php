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
}
