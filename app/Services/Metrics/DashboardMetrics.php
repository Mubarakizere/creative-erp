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
        $user = auth()->user();
        $query = $this->applyFilters(Milestone::query(), $filters);
        if ($user && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->accessibleBy($user);
            });
        }

        return [
            // Milestone Stats
            'total_milestones' => (clone $query)->count(),
            'active_milestones' => (clone $query)->whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_milestones' => (clone $query)->where('status', 'Completed')->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $user = auth()->user();
        $query = $this->applyFilters(Milestone::query(), $filters)->with('project');
        if ($user && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->accessibleBy($user);
            });
        }

        return [
            'latestMilestones' => $query->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
