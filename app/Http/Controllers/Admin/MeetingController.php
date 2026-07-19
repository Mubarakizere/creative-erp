<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Services\MeetingService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingController extends Controller
{
    use AuthorizesRequests;

    protected MeetingService $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * Display a listing of meetings.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Meeting::class);

        $query = Meeting::with(['company', 'branch', 'project', 'creator', 'attendees']);

        // Company scope
        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('attendees', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('meeting_type') && $request->meeting_type !== 'All') {
            $query->byType($request->meeting_type);
        }

        if ($request->filled('status') && $request->status !== 'All') {
            $query->byStatus($request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_at', '<=', $request->date_to);
        }

        // Sorting
        $sort = $request->get('sort', 'upcoming');
        $query = match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'upcoming' => $query->where('start_at', '>=', now())->orderBy('start_at'),
            'recently_updated' => $query->latest('updated_at'),
            'meeting_type' => $query->orderBy('meeting_type')->orderBy('start_at'),
            default => $query->orderBy('start_at', 'desc'),
        };

        $meetings = $query->paginate(25)->withQueryString();

        // Data for filters
        $companies = Company::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('admin.meetings.index', compact(
            'meetings', 'companies', 'branches', 'projects'
        ));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Meeting::class);

        $companies = Company::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('first_name')->get();

        $selectedProject = null;
        if ($request->filled('project_id')) {
            $selectedProject = Project::find($request->project_id);
        }

        $meetingableType = $request->query('meetingable_type');
        $meetingableId = $request->query('meetingable_id');

        return view('admin.meetings.create', compact(
            'companies', 'branches', 'projects', 'users', 'selectedProject', 'meetingableType', 'meetingableId'
        ));
    }

    /**
     * Store a newly created meeting.
     */
    public function store(StoreMeetingRequest $request)
    {
        $this->authorize('create', Meeting::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $attendeeIds = $data['attendees'] ?? [];
        unset($data['attendees']);

        try {
            // Check for conflicts
            $conflicts = $this->meetingService->detectConflictsForAttendees(
                $data['start_at'], $data['end_at'], $attendeeIds
            );

            $meeting = $this->meetingService->createMeeting($data, $attendeeIds);

            $message = 'Meeting created successfully.';
            if (!empty($conflicts)) {
                $conflictCount = count($conflicts);
                $message .= " Warning: {$conflictCount} attendee(s) have scheduling conflicts.";
            }

            return redirect()->route('admin.meetings.show', $meeting)
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified meeting.
     */
    public function show(Meeting $meeting)
    {
        $this->authorize('view', $meeting);
        $meeting->load(['company', 'branch', 'project', 'creator', 'updater', 'attendees']);

        // Get current user's attendance status
        $userAttendance = $meeting->attendees->where('id', auth()->id())->first();

        return view('admin.meetings.show', compact('meeting', 'userAttendance'));
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        $meeting->load(['attendees']);

        $companies = Company::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.meetings.edit', compact(
            'meeting', 'companies', 'branches', 'projects', 'users'
        ));
    }

    /**
     * Update the specified meeting.
     */
    public function update(UpdateMeetingRequest $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $attendeeIds = $data['attendees'] ?? null;
        unset($data['attendees']);

        try {
            $this->meetingService->updateMeeting($meeting, $data, $attendeeIds);
            return redirect()->route('admin.meetings.show', $meeting)
                ->with('success', 'Meeting updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified meeting.
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorize('delete', $meeting);

        $this->meetingService->deleteMeeting($meeting);

        return redirect()->route('admin.meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }

    /**
     * Restore a soft-deleted meeting.
     */
    public function restore(Meeting $meeting)
    {
        $this->authorize('restore', $meeting);

        $this->meetingService->restoreMeeting($meeting);

        return back()->with('success', 'Meeting restored successfully.');
    }

    /**
     * Cancel a meeting.
     */
    public function cancel(Meeting $meeting)
    {
        $this->authorize('cancel', $meeting);

        $this->meetingService->cancelMeeting($meeting);

        return back()->with('success', 'Meeting cancelled successfully.');
    }

    /**
     * Reschedule a meeting.
     */
    public function reschedule(Request $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);

        $request->validate([
            'start_at' => 'required|date|after_or_equal:now',
            'end_at' => 'required|date|after:start_at',
        ]);

        try {
            $this->meetingService->rescheduleMeeting($meeting, $request->start_at, $request->end_at);
            return back()->with('success', 'Meeting rescheduled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Invite attendees to a meeting.
     */
    public function invite(Request $request, Meeting $meeting)
    {
        $this->authorize('invite', $meeting);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $this->meetingService->inviteAttendees($meeting, $request->user_ids);

        return back()->with('success', 'Invitations sent successfully.');
    }

    /**
     * Respond to a meeting invitation (accept/decline/tentative).
     */
    public function respond(Request $request, Meeting $meeting)
    {
        $this->authorize('respond', $meeting);

        $request->validate([
            'response' => 'required|in:accepted,declined,tentative',
        ]);

        $user = auth()->user();

        match ($request->response) {
            'accepted' => $this->meetingService->acceptInvitation($meeting, $user),
            'declined' => $this->meetingService->declineInvitation($meeting, $user),
            'tentative' => $this->meetingService->tentativeResponse($meeting, $user),
        };

        return back()->with('success', 'Response recorded successfully.');
    }
}
