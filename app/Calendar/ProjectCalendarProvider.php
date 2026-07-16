<?php

namespace App\Calendar;

use App\Contracts\CalendarEventProvider;
use App\DataTransferObjects\CalendarEvent;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProjectCalendarProvider implements CalendarEventProvider
{
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection
    {
        $query = Project::query()
            ->whereNotNull('planned_end_date')
            ->whereBetween('planned_end_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotIn('status', ['Completed', 'Closed']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('project_manager_id', $userId)
                  ->orWhereHas('projectMembers', function ($q2) use ($userId) {
                      $q2->where('user_id', $userId)->where('status', 'Active');
                  });
            });
        }

        return $query->get()->map(function (Project $project) {
            $isOverdue = $project->planned_end_date->isPast();

            return new CalendarEvent(
                id: 'project-' . $project->id,
                title: '📁 ' . $project->name . ' (Deadline)',
                start: $project->planned_end_date->startOfDay(),
                end: $project->planned_end_date->endOfDay(),
                type: 'project',
                color: $isOverdue ? '#DC2626' : '#10B981', // Red if overdue, emerald otherwise
                url: route('admin.projects.show', $project),
                allDay: true,
                meta: [
                    'status' => $project->status,
                    'progress' => $project->progress,
                    'is_overdue' => $isOverdue,
                ],
            );
        });
    }

    public function getType(): string
    {
        return 'project';
    }

    public function getColor(): string
    {
        return '#10B981'; // Emerald
    }
}
