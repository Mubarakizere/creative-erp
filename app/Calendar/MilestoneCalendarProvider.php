<?php

namespace App\Calendar;

use App\Contracts\CalendarEventProvider;
use App\DataTransferObjects\CalendarEvent;
use App\Models\Milestone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MilestoneCalendarProvider implements CalendarEventProvider
{
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection
    {
        $query = Milestone::with('project')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', '!=', 'Completed');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $query->accessibleBy($user);
            }
        }

        return $query->get()->map(function (Milestone $milestone) {
            $isOverdue = $milestone->due_date->isPast();

            return new CalendarEvent(
                id: 'milestone-' . $milestone->id,
                title: '🏁 ' . $milestone->name,
                start: $milestone->due_date->startOfDay(),
                end: $milestone->due_date->endOfDay(),
                type: 'milestone',
                color: $isOverdue ? '#DC2626' : '#8B5CF6', // Red if overdue, purple otherwise
                url: route('admin.milestones.show', $milestone),
                allDay: true,
                meta: [
                    'status' => $milestone->status,
                    'priority' => $milestone->priority,
                    'project' => $milestone->project?->name,
                    'progress' => $milestone->progress,
                    'is_overdue' => $isOverdue,
                ],
            );
        });
    }

    public function getType(): string
    {
        return 'milestone';
    }

    public function getColor(): string
    {
        return '#8B5CF6'; // Purple
    }
}
