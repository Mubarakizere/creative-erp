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

        return view('admin.projects.index', compact('projects', 'companies', 'branches', 'clients'));
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
        $managers = User::where('status', 'active')->orderBy('name')->get(); // Ideally filter by role

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
        
        $project->load(['company', 'branch', 'client', 'manager', 'creator', 'updater']);

        return view('admin.projects.show', compact('project'));
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
        $managers = User::where('status', 'active')->orderBy('name')->get();

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
        
        $project->load(['company', 'branch', 'client', 'manager']);
        
        // Placeholder for timeline events (in the future: tasks, milestones, etc.)
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
        
        $events = $events->sortByDesc('date');

        return view('admin.projects.timeline', compact('project', 'events'));
    }
}
