<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Task;

class TaskMetrics implements MetricProvider
{
    public function cards(): array
    {
        $userId = auth()->id();
        
        return [
            'total_tasks' => Task::count(),
            'active_tasks' => Task::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'overdue_tasks' => Task::where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
            'my_tasks' => $userId ? Task::where('assigned_to', $userId)->count() : 0,
            'tasks_due_today' => Task::where('status', '!=', 'Completed')->whereDate('due_date', now()->toDateString())->count(),
            'tasks_due_this_week' => Task::where('status', '!=', 'Completed')->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'critical_tasks' => Task::where('status', '!=', 'Completed')->where('priority', 'Critical')->count(),
        ];
    }

    public function widgets(): array
    {
        $userId = auth()->id();

        return [
            'myAssignedTasks' => $userId ? Task::with('project')->where('assigned_to', $userId)->where('status', '!=', 'Completed')->latest()->take(5)->get() : [],
            'recentlyCreatedTasks' => Task::with('project')->latest()->take(5)->get(),
            'recentlyCompletedTasks' => Task::with('project')->where('status', 'Completed')->latest('completed_at')->take(5)->get(),
            'overdueTasksList' => Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->orderBy('due_date')->take(5)->get(),
            'upcomingDeadlines' => Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '>=', now())->orderBy('due_date')->take(5)->get(),
            'tasksWaitingReview' => Task::with('project')->where('status', 'Waiting Review')->latest()->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [
            // Task Summary data
        ];
    }
}
