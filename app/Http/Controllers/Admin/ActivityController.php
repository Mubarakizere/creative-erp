<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\CrmActivityService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ActivityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected CrmActivityService $activityService)
    {
        $this->authorizeResource(Activity::class, 'activity');
    }

    public function index(Request $request)
    {
        $activities = $this->activityService->getPaginatedActivities($request->all());
        return view('admin.crm.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.crm.activities.create');
    }

    public function store(Request $request)
    {
        $activity = $this->activityService->createActivity($request->all());
        return back()->with('success', 'Activity logged successfully.');
    }

    public function show(Activity $activity)
    {
        return view('admin.crm.activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        return view('admin.crm.activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $this->activityService->updateActivity($activity, $request->all());
        return back()->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $this->activityService->deleteActivity($activity);
        return back()->with('success', 'Activity removed successfully.');
    }
}
