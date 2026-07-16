<?php

namespace App\Calendar;

use App\Contracts\CalendarEventProvider;
use App\DataTransferObjects\CalendarEvent;
use App\Models\TimeEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TimeEntryCalendarProvider implements CalendarEventProvider
{
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection
    {
        $query = TimeEntry::with(['project', 'task'])
            ->where('status', 'completed')
            ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get()->map(function (TimeEntry $entry) {
            $title = '⏱️ ' . ($entry->task ? $entry->task->name : $entry->project->name);
            
            $durationStr = intdiv($entry->duration_minutes, 60) . 'h ' . ($entry->duration_minutes % 60) . 'm';
            if ($entry->description) {
                $title .= ' (' . $durationStr . ')';
            } else {
                $title .= ' (' . $durationStr . ')';
            }

            return new CalendarEvent(
                id: 'time-entry-' . $entry->id,
                title: $title,
                start: $entry->start_time,
                end: $entry->end_time ?? $entry->start_time->copy()->addMinutes($entry->duration_minutes),
                type: 'time_entry',
                color: $this->getColor(),
                url: route('admin.time-tracking.timesheet'), // Link back to timesheet
                allDay: false,
                meta: [
                    'project' => $entry->project?->name,
                    'task' => $entry->task?->name,
                    'duration' => $durationStr,
                    'billable' => $entry->billable,
                ],
            );
        });
    }

    public function getType(): string
    {
        return 'time_entry';
    }

    public function getColor(): string
    {
        return '#8B5CF6'; // Purple
    }
}
