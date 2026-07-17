<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Milestone;

class DashboardMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            // Milestone Stats
            'total_milestones' => Milestone::count(),
            'active_milestones' => Milestone::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_milestones' => Milestone::where('status', 'Completed')->count(),
        ];
    }

    public function widgets(): array
    {
        return [
            'latestMilestones' => Milestone::with('project')->latest()->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [];
    }
}
