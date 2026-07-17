<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class ReportMetrics
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get datasets for various report summaries.
     */
    public function getReportSummaries(): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $cacheKey = "metrics_report_summaries_{$userId}_{$companyId}";
        $ttl = config('metrics.cache_ttl.reports', 900);

        return Cache::remember($cacheKey, $ttl, function () {
            // Consume data from MetricsService to avoid duplicated calculations
            $cards = $this->metricsService->cards();
            $reports = $this->metricsService->reports();

            return [
                'project_summary' => [
                    'total' => $cards['projects'] ?? 0,
                    'active' => $cards['active_projects'] ?? 0,
                    'completed' => $cards['completed_projects'] ?? 0,
                ],
                'task_summary' => [
                    'total' => $cards['total_tasks'] ?? 0,
                    'completed' => $cards['completed_tasks'] ?? 0,
                    'overdue' => $cards['overdue_tasks'] ?? 0,
                ],
                'meeting_summary' => [
                    'today' => $cards['meetings_today'] ?? 0,
                    'upcoming' => $cards['upcoming_meetings'] ?? 0,
                ],
                'time_summary' => [
                    'hours_this_week' => $cards['hours_this_week'] ?? 0,
                    'billable_month' => $cards['billable_hours_month'] ?? 0,
                ],
                'client_summary' => [
                    'total' => $cards['clients'] ?? 0,
                ],
                'organization_summary' => [
                    'companies' => $cards['companies'] ?? 0,
                    'branches' => $cards['branches'] ?? 0,
                    'departments' => $cards['departments'] ?? 0,
                ],
            ];
        });
    }
}
