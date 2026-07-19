<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Milestone;

class DashboardMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        return [
            // Milestone Stats
            'total_milestones' => $this->applyFilters(Milestone::query(), $filters)->count(),
            'active_milestones' => $this->applyFilters(Milestone::query(), $filters)->whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_milestones' => $this->applyFilters(Milestone::query(), $filters)->where('status', 'Completed')->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        return [
            'latestMilestones' => $this->applyFilters(Milestone::query(), $filters)->with('project')->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
