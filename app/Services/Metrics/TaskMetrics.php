<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Task;

class TaskMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        $userId = auth()->id();
        
        return [
            'total_tasks' => $this->applyFilters(Task::query(), $filters)->count(),
            'active_tasks' => $this->applyFilters(Task::query(), $filters)->whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_tasks' => $this->applyFilters(Task::query(), $filters)->where('status', 'Completed')->count(),
            'overdue_tasks' => $this->applyFilters(Task::query(), $filters)->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
            'my_tasks' => $userId ? $this->applyFilters(Task::query(), $filters)->where('assigned_to', $userId)->count() : 0,
            'tasks_due_today' => $this->applyFilters(Task::query(), $filters)->where('status', '!=', 'Completed')->whereDate('due_date', now()->toDateString())->count(),
            'tasks_due_this_week' => $this->applyFilters(Task::query(), $filters)->where('status', '!=', 'Completed')->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'critical_tasks' => $this->applyFilters(Task::query(), $filters)->where('status', '!=', 'Completed')->where('priority', 'Critical')->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $userId = auth()->id();

        return [
            'myAssignedTasks' => $userId ? $this->applyFilters(Task::query(), $filters)->with('project')->where('assigned_to', $userId)->where('status', '!=', 'Completed')->latest()->take(5)->get() : [],
            'recentlyCreatedTasks' => $this->applyFilters(Task::query(), $filters)->with('project')->latest()->take(5)->get(),
            'recentlyCompletedTasks' => $this->applyFilters(Task::query(), $filters)->with('project')->where('status', 'Completed')->latest('completed_at')->take(5)->get(),
            'overdueTasksList' => $this->applyFilters(Task::query(), $filters)->with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->orderBy('due_date')->take(5)->get(),
            'upcomingDeadlines' => $this->applyFilters(Task::query(), $filters)->with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '>=', now())->orderBy('due_date')->take(5)->get(),
            'tasksWaitingReview' => $this->applyFilters(Task::query(), $filters)->with('project')->where('status', 'Waiting Review')->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            // Task Summary data
        ];
    }
}
