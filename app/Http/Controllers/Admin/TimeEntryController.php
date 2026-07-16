<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Task;
use App\Services\TimeTrackingService;
use App\Services\TimerService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TimeEntryController extends Controller
{
    use AuthorizesRequests;

    protected $timeService;
    protected $timerService;

    public function __construct(TimeTrackingService $timeService, TimerService $timerService)
    {
        $this->timeService = $timeService;
        $this->timerService = $timerService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', TimeEntry::class);

        $query = TimeEntry::with(['project', 'task', 'user', 'company'])
            ->where('status', 'completed');

        if (auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        // Apply filters
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->user_id && auth()->user()->can('time.approve')) {
            $query->where('user_id', $request->user_id);
        } elseif (!auth()->user()->can('time.approve')) {
            $query->where('user_id', auth()->id());
        }

        $entries = $query->latest('start_time')->paginate(25)->withQueryString();

        return view('admin.time-tracking.index', compact('entries'));
    }
    
    public function timesheet(Request $request)
    {
        $this->authorize('viewAny', TimeEntry::class);
        
        $period = $request->get('period', 'monthly');
        $now = now();
        
        $query = TimeEntry::with(['project', 'task'])
            ->where('user_id', auth()->id())
            ->where('status', 'completed');
            
        if ($period === 'daily') {
            $query->whereDate('start_time', $now->toDateString());
        } elseif ($period === 'weekly') {
            $query->whereBetween('start_time', [$now->startOfWeek(), $now->endOfWeek()]);
        } elseif ($period === 'monthly') {
            $query->whereMonth('start_time', $now->month)->whereYear('start_time', $now->year);
        }

        $entries = $query->latest('start_time')->paginate(25)->withQueryString();
        
        $totalMinutes = TimeEntry::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->when($period === 'daily', fn($q) => $q->whereDate('start_time', $now->toDateString()))
            ->when($period === 'weekly', fn($q) => $q->whereBetween('start_time', [$now->startOfWeek(), $now->endOfWeek()]))
            ->when($period === 'monthly', fn($q) => $q->whereMonth('start_time', $now->month)->whereYear('start_time', $now->year))
            ->sum('duration_minutes');
            
        return view('admin.time-tracking.timesheet', compact('entries', 'period', 'totalMinutes'));
    }
    
    public function reports(Request $request)
    {
        $this->authorize('export', TimeEntry::class);
        return view('admin.time-tracking.reports');
    }

    public function store(Request $request)
    {
        $this->authorize('create', TimeEntry::class);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'billable' => 'boolean',
        ]);

        try {
            $this->timeService->createEntry($validated);
            return redirect()->back()->with('success', 'Time entry logged successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'billable' => 'boolean',
        ]);

        try {
            $this->timeService->updateEntry($timeEntry, $validated);
            return redirect()->back()->with('success', 'Time entry updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $this->authorize('delete', $timeEntry);
        $timeEntry->delete();
        return redirect()->back()->with('success', 'Time entry deleted successfully.');
    }
    
    // Timer Actions
    public function startTimer(Request $request)
    {
        $this->authorize('create', TimeEntry::class);
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'nullable|string',
            'billable' => 'boolean',
        ]);

        try {
            $this->timerService->startTimer($validated);
            return redirect()->back()->with('success', 'Timer started.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function stopTimer(TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        try {
            $this->timerService->stopTimer($timeEntry);
            return redirect()->back()->with('success', 'Timer stopped and time logged.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function pauseTimer(TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        try {
            $this->timerService->pauseTimer($timeEntry);
            return redirect()->back()->with('success', 'Timer paused.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function resumeTimer(TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        try {
            $this->timerService->resumeTimer($timeEntry);
            return redirect()->back()->with('success', 'Timer resumed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
