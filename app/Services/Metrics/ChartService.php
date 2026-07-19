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
    public function getChartData(array $filters = []): array
    {
        $userId = auth()->id() ?? 'guest';
        $companyId = auth()->user()?->company_id ?? 'all';
        $filterHash = !empty($filters) ? '_' . md5(json_encode($filters)) : '';
        $cacheKey = "metrics_charts_{$userId}_{$companyId}{$filterHash}";
        
        $ttl = !empty($filters) ? 60 : config('metrics.cache_ttl.charts', 600);

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            return [
                'tasksByStatus' => $this->tasksByStatus($filters),
                'tasksByPriority' => $this->tasksByPriority($filters),
                'projectProgress' => $this->projectProgress($filters),
                
                // Historical placeholders (would ideally be filtered queries as well)
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

    private function applyFilters($query, array $filters, string $relation = null)
    {
        $prefix = $relation ? $relation . '.' : '';

        if (!empty($filters['company_id'])) {
            $query->whereIn($prefix . 'company_id', (array) $filters['company_id']);
        }
        if (!empty($filters['branch_id'])) {
            $query->whereIn($prefix . 'branch_id', (array) $filters['branch_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->whereIn($prefix . 'department_id', (array) $filters['department_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate($prefix . 'created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate($prefix . 'created_at', '<=', $filters['date_to']);
        }
        if (!empty($filters['project_id'])) {
            $query->whereIn($prefix . 'project_id', (array) $filters['project_id']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->whereIn($prefix . 'assigned_to', (array) $filters['assigned_to']);
        }
        
        return $query;
    }

    private function tasksByStatus(array $filters = []): array
    {
        return [
            'Pending' => $this->applyFilters(\App\Models\Task::where('status', 'Pending'), $filters)->count(),
            'In Progress' => $this->applyFilters(\App\Models\Task::where('status', 'In Progress'), $filters)->count(),
            'Waiting Review' => $this->applyFilters(\App\Models\Task::where('status', 'Waiting Review'), $filters)->count(),
            'Completed' => $this->applyFilters(\App\Models\Task::where('status', 'Completed'), $filters)->count(),
            'On Hold' => $this->applyFilters(\App\Models\Task::where('status', 'On Hold'), $filters)->count(),
        ];
    }

    private function tasksByPriority(array $filters = []): array
    {
        return [
            'Low' => $this->applyFilters(\App\Models\Task::where('priority', 'Low'), $filters)->count(),
            'Medium' => $this->applyFilters(\App\Models\Task::where('priority', 'Medium'), $filters)->count(),
            'High' => $this->applyFilters(\App\Models\Task::where('priority', 'High'), $filters)->count(),
            'Critical' => $this->applyFilters(\App\Models\Task::where('priority', 'Critical'), $filters)->count(),
        ];
    }

    private function projectProgress(array $filters = []): array
    {
        $query = \App\Models\Project::whereIn('status', ['Planning', 'In Progress'])->take(5);
        $this->applyFilters($query, $filters);
        
        $projects = $query->pluck('progress', 'name')->toArray();
        return !empty($projects) ? $projects : ['No Active Projects' => 0];
    }
}
