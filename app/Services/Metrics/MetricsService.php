<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class MetricsService
{
    /**
     * @var \App\Contracts\MetricProvider[]
     */
    protected array $providers = [];

    public function __construct(
        DashboardMetrics $dashboardMetrics,
        OrganizationMetrics $organizationMetrics,
        UserMetrics $userMetrics,
        ClientMetrics $clientMetrics,
        ProjectMetrics $projectMetrics,
        TaskMetrics $taskMetrics,
        MeetingMetrics $meetingMetrics,
        TimeMetrics $timeMetrics,
        DocumentMetrics $documentMetrics,
        DiscussionMetrics $discussionMetrics,
        WorkflowMetrics $workflowMetrics
    ) {
        $this->providers = [
            $dashboardMetrics,
            $organizationMetrics,
            $userMetrics,
            $clientMetrics,
            $projectMetrics,
            $taskMetrics,
            $meetingMetrics,
            $timeMetrics,
            $documentMetrics,
            $discussionMetrics,
            $workflowMetrics
        ];
    }

    /**
     * Get aggregated dashboard statistics (cards).
     */
    public function cards(): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $cacheKey = "metrics_cards_{$userId}_{$companyId}";
        $ttl = config('metrics.cache_ttl.dashboard', 300);

        return Cache::remember($cacheKey, $ttl, function () {
            $cards = [];
            foreach ($this->providers as $provider) {
                $cards = array_merge($cards, $provider->cards());
            }
            return $cards;
        });
    }

    /**
     * Get aggregated dashboard widgets data.
     */
    public function widgets(): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $cacheKey = "metrics_widgets_{$userId}_{$companyId}";
        $ttl = config('metrics.cache_ttl.dashboard', 300);

        return Cache::remember($cacheKey, $ttl, function () {
            $widgets = [];
            foreach ($this->providers as $provider) {
                $widgets = array_merge($widgets, $provider->widgets());
            }
            return $widgets;
        });
    }

    /**
     * Get aggregated report data.
     */
    public function reports(): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $cacheKey = "metrics_reports_{$userId}_{$companyId}";
        $ttl = config('metrics.cache_ttl.reports', 900);

        return Cache::remember($cacheKey, $ttl, function () {
            $reports = [];
            foreach ($this->providers as $provider) {
                $reports = array_merge($reports, $provider->reports());
            }
            return $reports;
        });
    }

    /**
     * Get the full dashboard data structure.
     */
    public function dashboard(): array
    {
        // Inject ChartService here to avoid circular dependency in constructor if any,
        // though ChartService will depend on MetricsService, not the other way around.
        $chartService = app(ChartService::class);

        return array_merge([
            'stats' => $this->cards(),
            'chartData' => $chartService->getChartData(),
        ], $this->widgets());
    }
}
