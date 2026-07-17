<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Cache;

class ChartService
{
    protected MetricsService $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Get all datasets required for dashboard charts.
     */
    public function getChartData(): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $cacheKey = "metrics_charts_{$userId}_{$companyId}";
        $ttl = config('metrics.cache_ttl.charts', 600);

        return Cache::remember($cacheKey, $ttl, function () {
            // Note: ChartService consumes data from MetricsService 
            // or calculates based on already fetched metrics to prevent duplicate queries.
            // Since we need chart specific aggregations, we'll implement them here
            // but rely on cached queries or aggregated data where possible.
            // For now, implementing the same placeholder logic as before but organized.

            return [
                'tasksByStatus' => $this->tasksByStatus(),
                'tasksByPriority' => $this->tasksByPriority(),
                'projectProgress' => $this->projectProgress(),
                
                // Historical placeholders
                'tasksPerProject' => [12, 19, 3, 5, 2, 3],
                'monthlyTaskCompletion' => [65, 59, 80, 81, 56, 55, 40],
                'commentsPerModule' => [30, 40, 15, 15],
                'commentsPerUser' => [12, 19, 14, 5, 2],
                'dailyDiscussions' => [5, 10, 15, 8, 12, 20, 25],
                'monthlyDiscussions' => [50, 60, 45, 70, 90, 80],
                'mentionsPerMonth' => [10, 15, 5, 20, 25, 30],
                
                // Meeting Charts
                'meetingsPerMonth' => [4, 8, 15, 12, 20, 18, 25],
                'meetingsByType' => [10, 5, 8, 3, 2, 4],
                'attendanceRate' => [95, 92, 88, 96, 90],
            ];
        });
    }

    private function tasksByStatus(): array
    {
        // Ideally, this can consume from $this->metricsService->cards() if those were broken down,
        // but since we need a specific array format for charts, we query it. 
        // We use Eloquent but these could be further optimized.
        return [
            \App\Models\Task::where('status', 'Pending')->count(),
            \App\Models\Task::where('status', 'In Progress')->count(),
            \App\Models\Task::where('status', 'Waiting Review')->count(),
            \App\Models\Task::where('status', 'Completed')->count(),
            \App\Models\Task::where('status', 'On Hold')->count(),
        ];
    }

    private function tasksByPriority(): array
    {
        return [
            \App\Models\Task::where('priority', 'Low')->count(),
            \App\Models\Task::where('priority', 'Medium')->count(),
            \App\Models\Task::where('priority', 'High')->count(),
            \App\Models\Task::where('priority', 'Critical')->count(),
        ];
    }

    private function projectProgress(): array
    {
        return \App\Models\Project::whereIn('status', ['Planning', 'In Progress'])->take(5)->pluck('progress')->toArray() ?: [0];
    }
}
