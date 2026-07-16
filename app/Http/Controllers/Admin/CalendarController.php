<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    protected CalendarService $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the calendar view.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $view = $request->get('view', 'month'); // month, week, day

        $userId = auth()->id();
        $companyId = auth()->user()->company_id;

        // For Super Admin, don't filter by company
        if (auth()->user()->hasRole('Super Admin')) {
            $companyId = null;
        }

        $events = $this->calendarService->getMonthEvents($year, $month, $userId, $companyId);
        $todaysSchedule = $this->calendarService->getTodaysSchedule($userId, $companyId);
        $upcomingEvents = $this->calendarService->getUpcomingEvents($userId, $companyId, 5);
        $legend = $this->calendarService->getProviderLegend();

        return view('admin.calendar.index', compact(
            'events', 'todaysSchedule', 'upcomingEvents', 'legend',
            'year', 'month', 'view'
        ));
    }

    /**
     * JSON endpoint for calendar AJAX requests.
     */
    public function events(Request $request)
    {
        $start = Carbon::parse($request->get('start', now()->startOfMonth()));
        $end = Carbon::parse($request->get('end', now()->endOfMonth()));
        $types = $request->get('types') ? explode(',', $request->get('types')) : null;

        $userId = auth()->id();
        $companyId = auth()->user()->company_id;

        if (auth()->user()->hasRole('Super Admin')) {
            $companyId = null;
        }

        $events = $this->calendarService->getEvents($start, $end, $userId, $companyId, $types);

        return response()->json([
            'events' => $events->map->toArray()->values(),
        ]);
    }

    /**
     * Display the agenda view.
     */
    public function agenda(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : now();

        $userId = auth()->id();
        $companyId = auth()->user()->company_id;

        if (auth()->user()->hasRole('Super Admin')) {
            $companyId = null;
        }

        $events = $this->calendarService->getAgenda($date, $userId, $companyId);

        // Also get the week's events for the mini sidebar
        $weekEvents = $this->calendarService->getWeekEvents($userId, $companyId);

        return view('admin.calendar.agenda', compact('events', 'weekEvents', 'date'));
    }

    /**
     * Display upcoming events view.
     */
    public function upcoming(Request $request)
    {
        $userId = auth()->id();
        $companyId = auth()->user()->company_id;

        if (auth()->user()->hasRole('Super Admin')) {
            $companyId = null;
        }

        $days = $request->get('days', 7);
        $start = now();
        $end = now()->addDays($days);

        $events = $this->calendarService->getEvents($start, $end, $userId, $companyId);

        return view('admin.calendar.upcoming', compact('events', 'days'));
    }
}
