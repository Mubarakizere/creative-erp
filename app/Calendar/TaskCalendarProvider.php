<?php

namespace App\Calendar;

use App\Contracts\CalendarEventProvider;
use App\DataTransferObjects\CalendarEvent;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TaskCalendarProvider implements CalendarEventProvider
{
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection
    {
        $query = Task::with('project')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', '!=', 'Completed');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function (Task $task) {
            $isOverdue = $task->due_date->isPast();

            return new CalendarEvent(
                id: 'task-' . $task->id,
                title: '📋 ' . $task->name,
                start: $task->due_date->startOfDay(),
                end: $task->due_date->endOfDay(),
                type: 'task',
                color: $isOverdue ? '#EF4444' : '#F59E0B', // Red if overdue, amber otherwise
                url: route('admin.projects.tasks.show', $task),
                allDay: true,
                meta: [
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'project' => $task->project?->name,
                    'is_overdue' => $isOverdue,
                ],
            );
        });
    }

    public function getType(): string
    {
        return 'task';
    }

    public function getColor(): string
    {
        return '#F59E0B'; // Amber
    }
}
