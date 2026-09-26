<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Client;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $projects = $this->projectService->list($request->only([
            'search', 'status', 'priority', 'company_id', 'branch_id', 'client_id', 'trashed',
        ]));

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        // Initially load all, or load via AJAX. We'll load all active for simplicity.
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('display_name')->get();

        $user = auth()->user();
        $baseQuery = Project::query()->accessibleBy($user);
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'In Progress')->count(),
            'planning_pending' => (clone $baseQuery)->whereIn('status', ['Planning', 'Pending'])->count(),
            'completed_closed' => (clone $baseQuery)->whereIn('status', ['Completed', 'Closed'])->count(),
            'on_hold' => (clone $baseQuery)->where('status', 'On Hold')->count(),
        ];

        return view('admin.projects.index', compact('projects', 'companies', 'branches', 'clients', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Project::class);
        
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('display_name')->get();
        $managers = User::where('status', 'active')->orderBy('first_name')->get(); // Ideally filter by role

        return view('admin.projects.create', compact('companies', 'branches', 'clients', 'managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $project = $this->projectService->create($request->validated());

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        Gate::authorize('view', $project);
        
        $project->load([
            'company', 'branch', 'client', 'manager', 'creator', 'updater',
            'tasks.materialRequests.items.product',
            'tasks.materialIssues.items.product',
            'materialRequests.items.product',
            'materialIssues.items.product',
            'milestones', 'documents', 'timeEntries', 'comments.user',
            'expenses.creator', 'expenses.user', 'expenses.task', 'expenses.budgetLine',
            'activeBudget.lines.task', 'activeBudget.lines.category', 'activeBudget.lines.product'
        ]);
        
        $financialService = app(\App\Services\ProjectFinancialService::class);
        $financialSummary = $financialService->getProjectFinancialSummary($project);
        $projectBudgetAnalysis = app(\App\Services\Finance\BudgetService::class)->getProjectBudgetAnalysis($project);
        $teamUserIds = $project->projectMembers()->pluck('user_id')->push($project->project_manager_id)->filter()->unique();
        $teamUsers = \App\Models\User::whereIn('id', $teamUserIds)->orderBy('first_name')->get();
        if ($teamUsers->isEmpty()) {
            $teamUsers = \App\Models\User::active()
                ->when($project->company_id, fn($q) => $q->where('company_id', $project->company_id))
                ->orderBy('first_name')
                ->get();
        }


        
        $activityLogs = \App\Models\ActivityLog::with('user')
            ->where('subject_type', Project::class)
            ->where('subject_id', $project->id)
            ->latest()
            ->take(50)
            ->get();
        
        // Build timeline events
        $events = collect([
            [
                'date' => $project->created_at,
                'title' => 'Project Created',
                'description' => "Project {$project->project_code} was created by " . optional($project->creator)->name,
                'type' => 'created',
                'icon' => 'plus'
            ]
        ]);
        
        if ($project->status === 'Closed') {
            $events->push([
                'date' => $project->actual_end_date ?? $project->updated_at,
                'title' => 'Project Closed',
                'description' => "Project was marked as Closed.",
                'type' => 'closed',
                'icon' => 'check-circle'
            ]);
        }
        
        foreach($project->milestones as $milestone) {
            $events->push([
                'date' => $milestone->created_at,
                'title' => 'Milestone Added',
                'description' => "Milestone '{$milestone->name}' was added.",
                'type' => 'info',
                'icon' => 'flag'
            ]);
        }
        
        foreach($project->tasks as $task) {
            $events->push([
                'date' => $task->created_at,
                'title' => 'Task Created',
                'description' => "Task '{$task->title}' was created.",
                'type' => 'info',
                'icon' => 'clipboard-list'
            ]);
            if ($task->status === 'completed' && $task->updated_at) {
                $events->push([
                    'date' => $task->updated_at,
                    'title' => 'Task Completed',
                    'description' => "Task '{$task->title}' was completed.",
                    'type' => 'success',
                    'icon' => 'check'
                ]);
            }
        }
        
        foreach($project->documents as $doc) {
            $events->push([
                'date' => $doc->created_at,
                'title' => 'Document Uploaded',
                'description' => "Document '{$doc->title}' was uploaded.",
                'type' => 'info',
                'icon' => 'document'
            ]);
        }

        foreach($project->expenses as $exp) {
            $events->push([
                'date' => $exp->created_at,
                'title' => 'Expense Logged: ' . $exp->title,
                'description' => "Category: {$exp->category} - Amount: " . format_currency($exp->amount, $project->currency),
                'type' => 'info',
                'icon' => 'document'
            ]);
        }
        
        $timelineEvents = $events->sortByDesc('date');

        return view('admin.projects.show', compact('project', 'timelineEvents', 'activityLogs', 'financialSummary', 'projectBudgetAnalysis', 'teamUsers'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);
        
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('display_name')->get();
        $managers = User::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.projects.edit', compact('project', 'companies', 'branches', 'clients', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $this->projectService->update($project, $request->validated());

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project archived successfully.');
    }

    /**
     * Restore a soft-deleted project.
     */
    public function restore(int $id): RedirectResponse
    {
        $project = Project::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $project);

        $this->projectService->restore($project);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project restored successfully.');
    }

    /**
     * Close a project.
     */
    public function close(Project $project): RedirectResponse
    {
        Gate::authorize('close', $project);

        $this->projectService->close($project);

        return redirect()
            ->back()
            ->with('success', 'Project closed successfully.');
    }

    /**
     * Reopen a project.
     */
    public function reopen(Project $project): RedirectResponse
    {
        Gate::authorize('reopen', $project);

        $this->projectService->reopen($project);

        return redirect()
            ->back()
            ->with('success', 'Project reopened successfully.');
    }
    
    /**
     * Duplicate a project.
     */
    public function duplicate(Project $project): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $newProject = $this->projectService->duplicate($project);

        return redirect()
            ->route('admin.projects.edit', $newProject)
            ->with('success', 'Project duplicated. Please review the copied details.');
    }
    
    /**
     * Display project timeline.
     */
    public function timeline(Project $project): View
    {
        Gate::authorize('view', $project);
        
        $project->load([
            'company', 'branch', 'client', 'manager', 'creator', 'updater',
            'tasks', 'milestones', 'documents', 'timeEntries'
        ]);
        
        // Build timeline events
        $events = collect([
            [
                'date' => $project->created_at,
                'title' => 'Project Created',
                'description' => "Project {$project->project_code} was created by " . optional($project->creator)->name,
                'type' => 'created',
                'icon' => 'plus'
            ]
        ]);
        
        if ($project->status === 'Closed') {
            $events->push([
                'date' => $project->actual_end_date ?? $project->updated_at,
                'title' => 'Project Closed',
                'description' => "Project was marked as Closed.",
                'type' => 'closed',
                'icon' => 'check-circle'
            ]);
        }
        
        foreach($project->milestones as $milestone) {
            $events->push([
                'date' => $milestone->created_at,
                'title' => 'Milestone Added',
                'description' => "Milestone '{$milestone->name}' was added.",
                'type' => 'info',
                'icon' => 'flag'
            ]);
        }
        
        foreach($project->tasks as $task) {
            $events->push([
                'date' => $task->created_at,
                'title' => 'Task Created',
                'description' => "Task '{$task->title}' was created.",
                'type' => 'info',
                'icon' => 'clipboard-list'
            ]);
            if ($task->status === 'completed' && $task->updated_at) {
                $events->push([
                    'date' => $task->updated_at,
                    'title' => 'Task Completed',
                    'description' => "Task '{$task->title}' was completed.",
                    'type' => 'success',
                    'icon' => 'check'
                ]);
            }
        }
        
        foreach($project->documents as $doc) {
            $events->push([
                'date' => $doc->created_at,
                'title' => 'Document Uploaded',
                'description' => "Document '{$doc->title}' was uploaded.",
                'type' => 'info',
                'icon' => 'document'
            ]);
        }
        
        $events = $events->sortByDesc('date');

        return view('admin.projects.timeline', compact('project', 'events'));
    }

    /**
     * Quick update project status.
     */
    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'status' => 'required|string|in:Planning,Pending,In Progress,On Hold,Completed,Cancelled,Closed',
        ]);

        $newStatus = $validated['status'];
        $updateData = [
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ];

        if ($newStatus === 'Closed' || $newStatus === 'Completed') {
            if (!$project->actual_end_date) {
                $updateData['actual_end_date'] = now();
            }
            if ($newStatus === 'Closed') {
                $updateData['progress'] = 100;
            }
        } elseif ($newStatus === 'In Progress' && $project->status === 'Closed') {
            $updateData['actual_end_date'] = null;
        }

        $project->update($updateData);

        return redirect()->back()->with('success', "Project status updated to '{$newStatus}' successfully.");
    }
}
