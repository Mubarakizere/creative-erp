<?php

namespace App\Services;

use App\Contracts\CalendarEventProvider;
use App\Calendar\MeetingCalendarProvider;
use App\Calendar\TaskCalendarProvider;
use App\Calendar\MilestoneCalendarProvider;
use App\Calendar\ProjectCalendarProvider;
use App\Calendar\TimeEntryCalendarProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Registered calendar event providers.
     *
     * @var CalendarEventProvider[]
     */
    protected array $providers = [];

    public function __construct()
    {
        // Register default providers
        $this->registerProvider(new MeetingCalendarProvider());
        $this->registerProvider(new TaskCalendarProvider());
        $this->registerProvider(new MilestoneCalendarProvider());
        $this->registerProvider(new ProjectCalendarProvider());
        $this->registerProvider(new TimeEntryCalendarProvider());
    }

    /**
     * Register a calendar event provider.
     */
    public function registerProvider(CalendarEventProvider $provider): void
    {
        $this->providers[$provider->getType()] = $provider;
    }

    /**
     * Get all calendar events within a date range.
     */
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null, ?array $types = null): Collection
    {
        $events = collect();

        foreach ($this->providers as $type => $provider) {
            // If types filter is set, only include matching providers
            if ($types !== null && !in_array($type, $types)) {
                continue;
            }

            $events = $events->merge($provider->getEvents($start, $end, $userId, $companyId));
        }

        return $events->sortBy('start');
    }

    /**
     * Get events for a specific user.
     */
    public function getEventsForUser(int $userId, Carbon $start, Carbon $end, ?int $companyId = null): Collection
    {
        return $this->getEvents($start, $end, $userId, $companyId);
    }

    /**
     * Get today's schedule for a user.
     */
    public function getTodaysSchedule(?int $userId = null, ?int $companyId = null): Collection
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        return $this->getEvents($start, $end, $userId, $companyId);
    }

    /**
     * Get upcoming events (next 7 days).
     */
    public function getUpcomingEvents(?int $userId = null, ?int $companyId = null, int $limit = 10): Collection
    {
        $start = now();
        $end = now()->addDays(7);

        return $this->getEvents($start, $end, $userId, $companyId)->take($limit);
    }

    /**
     * Get events for the current week.
     */
    public function getWeekEvents(?int $userId = null, ?int $companyId = null): Collection
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        return $this->getEvents($start, $end, $userId, $companyId);
    }

    /**
     * Get agenda for a specific date.
     */
    public function getAgenda(Carbon $date, ?int $userId = null, ?int $companyId = null): Collection
    {
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        return $this->getEvents($start, $end, $userId, $companyId);
    }

    /**
     * Get month events for calendar grid.
     */
    public function getMonthEvents(int $year, int $month, ?int $userId = null, ?int $companyId = null): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth()->startOfWeek();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfWeek();

        return $this->getEvents($start, $end, $userId, $companyId);
    }

    /**
     * Get events count by type for the current week (used for dashboard).
     */
    public function getWeekEventCounts(?int $companyId = null): array
    {
        $events = $this->getWeekEvents(null, $companyId);

        return [
            'total' => $events->count(),
            'meetings' => $events->where('type', 'meeting')->count(),
            'tasks' => $events->where('type', 'task')->count(),
            'milestones' => $events->where('type', 'milestone')->count(),
            'projects' => $events->where('type', 'project')->count(),
        ];
    }

    /**
     * Get the list of registered provider types with their colors.
     */
    public function getProviderLegend(): array
    {
        $legend = [];
        foreach ($this->providers as $type => $provider) {
            $legend[$type] = [
                'type' => $type,
                'color' => $provider->getColor(),
                'label' => ucfirst($type) . 's',
            ];
        }
        return $legend;
    }
}
