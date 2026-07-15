<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function __construct(
        protected \App\Services\MilestoneService $milestoneService
    ) {}

    public function index(Request $request): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('viewAny', \App\Models\Milestone::class);

        $query = \App\Models\Milestone::with(['project', 'company'])->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $milestones = $query->paginate(15);
        $projects = \App\Models\Project::orderBy('name')->get();

        return view('admin.milestones.index', compact('milestones', 'projects'));
    }

    public function create(): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Milestone::class);
        $projects = \App\Models\Project::orderBy('name')->get();
        return view('admin.milestones.create', compact('projects'));
    }

    public function store(\App\Http\Requests\Admin\StoreMilestoneRequest $request): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Milestone::class);

        $data = $request->validated();
        $project = \App\Models\Project::findOrFail($data['project_id']);
        $data['company_id'] = $project->company_id;
        $data['created_by'] = auth()->id();
        
        $milestone = $this->milestoneService->createMilestone($data);

        return redirect()
            ->route('admin.milestones.show', $milestone)
            ->with('success', 'Milestone created successfully.');
    }

    public function show(\App\Models\Milestone $milestone): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $milestone);
        $milestone->load(['project', 'company', 'tasks']);

        $projectTasks = \App\Models\Task::where('project_id', $milestone->project_id)
            ->whereNull('deleted_at')
            ->whereDoesntHave('milestones', function($q) use ($milestone) {
                $q->where('milestones.id', $milestone->id);
            })->get();

        return view('admin.milestones.show', compact('milestone', 'projectTasks'));
    }

    public function edit(\App\Models\Milestone $milestone): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $milestone);
        $projects = \App\Models\Project::orderBy('name')->get();
        return view('admin.milestones.edit', compact('milestone', 'projects'));
    }

    public function update(\App\Http\Requests\Admin\UpdateMilestoneRequest $request, \App\Models\Milestone $milestone): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $milestone);
        
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->milestoneService->updateMilestone($milestone, $data);

        return redirect()
            ->route('admin.milestones.show', $milestone)
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(\App\Models\Milestone $milestone): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $milestone);
        $this->milestoneService->deleteMilestone($milestone);

        return redirect()
            ->route('admin.milestones.index')
            ->with('success', 'Milestone archived successfully.');
    }

    public function restore(int $id): \Illuminate\Http\RedirectResponse
    {
        $milestone = \App\Models\Milestone::withTrashed()->findOrFail($id);
        \Illuminate\Support\Facades\Gate::authorize('restore', $milestone);
        
        $this->milestoneService->restoreMilestone($milestone);

        return redirect()
            ->route('admin.milestones.show', $milestone)
            ->with('success', 'Milestone restored successfully.');
    }

    public function duplicate(\App\Models\Milestone $milestone): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Milestone::class);
        $newMilestone = $this->milestoneService->duplicateMilestone($milestone);

        return redirect()
            ->route('admin.milestones.edit', $newMilestone)
            ->with('success', 'Milestone duplicated successfully.');
    }

    public function assignTasks(Request $request, \App\Models\Milestone $milestone): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('assignTasks', $milestone);
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id'
        ]);

        try {
            $this->milestoneService->assignTasks($milestone, $request->task_ids);
            return redirect()->back()->with('success', 'Tasks assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeTask(\App\Models\Milestone $milestone, int $taskId): \Illuminate\Http\RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('assignTasks', $milestone);
        
        $this->milestoneService->removeTask($milestone, $taskId);
        
        return redirect()->back()->with('success', 'Task removed from milestone.');
    }

    public function timeline(\App\Models\Milestone $milestone): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $milestone);
        $milestone->load(['project', 'tasks' => function($q) {
            $q->orderBy('due_date');
        }]);

        return view('admin.milestones.timeline', compact('milestone'));
    }

    public function activity(\App\Models\Milestone $milestone): \Illuminate\View\View
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $milestone);
        $milestone->load(['project']);
        
        $activities = [];

        return view('admin.milestones.activity', compact('milestone', 'activities'));
    }
}
