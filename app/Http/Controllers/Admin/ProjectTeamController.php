<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Models\Department;
use App\Services\ProjectTeamService;
use App\Http\Requests\Admin\StoreProjectMemberRequest;
use App\Http\Requests\Admin\UpdateProjectMemberRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ProjectTeamController extends Controller
{
    protected ProjectTeamService $teamService;

    public function __construct(ProjectTeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of project teams across all projects.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectMember::class);

        $query = ProjectMember::with(['project', 'user', 'department'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('project', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->project_id, function ($q, $projectId) {
                $q->where('project_id', $projectId);
            })
            ->when($request->department_id, function ($q, $departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            });

        $members = $query->latest('joined_at')->paginate(25)->withQueryString();
        
        $projects = Project::select('id', 'name')->orderBy('name')->get();
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        return view('admin.projects.team.index', compact('members', 'projects', 'departments'));
    }

    /**
     * Show the form for creating a new project member (assigning a user to a project).
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', ProjectMember::class);
        
        $projects = Project::select('id', 'name')->orderBy('name')->get();
        $users = User::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('admin.projects.team.create', compact('projects', 'users', 'departments', 'selectedProject'));
    }

    /**
     * Store a newly created project member in storage.
     */
    public function store(StoreProjectMemberRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->project_id);
        
        try {
            $this->teamService->assignMember($project, $request->validated());
            return redirect()->route('admin.projects.show', $project)->with('success', 'Team member assigned successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while assigning the team member: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified project member.
     */
    public function show(ProjectMember $teamMember): View
    {
        Gate::authorize('view', $teamMember);
        
        $teamMember->load(['project', 'user', 'department', 'creator', 'updater']);
        
        return view('admin.projects.team.show', compact('teamMember'));
    }

    /**
     * Show the form for editing the specified project member.
     */
    public function edit(ProjectMember $teamMember): View
    {
        Gate::authorize('update', $teamMember);
        
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        $teamMember->load(['project', 'user']);

        return view('admin.projects.team.edit', compact('teamMember', 'departments'));
    }

    /**
     * Update the specified project member in storage.
     */
    public function update(UpdateProjectMemberRequest $request, ProjectMember $teamMember): RedirectResponse
    {
        try {
            $this->teamService->updateAssignment($teamMember, $request->validated());
            return redirect()->route('admin.projects.show', $teamMember->project_id)->with('success', 'Team member updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while updating the team member: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified project member from storage (soft delete).
     */
    public function destroy(ProjectMember $teamMember): RedirectResponse
    {
        Gate::authorize('remove', $teamMember);
        
        $projectId = $teamMember->project_id;
        $this->teamService->removeMember($teamMember);
        
        return redirect()->route('admin.projects.show', $projectId)->with('success', 'Team member removed successfully.');
    }
    
    /**
     * Restore a removed project member.
     */
    public function restore(int $id): RedirectResponse
    {
        $teamMember = ProjectMember::withTrashed()->findOrFail($id);
        Gate::authorize('restore', $teamMember);
        
        $this->teamService->restoreMember($teamMember);
        
        return back()->with('success', 'Team member restored successfully.');
    }
    
    /**
     * Activate a project member.
     */
    public function activate(ProjectMember $teamMember): RedirectResponse
    {
        Gate::authorize('activate', $teamMember);
        
        try {
            $this->teamService->activateMember($teamMember);
            return back()->with('success', 'Team member activated successfully.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->validator->errors()->first());
        }
    }
    
    /**
     * Deactivate a project member.
     */
    public function deactivate(ProjectMember $teamMember): RedirectResponse
    {
        Gate::authorize('deactivate', $teamMember);
        
        $this->teamService->deactivateMember($teamMember);
        
        return back()->with('success', 'Team member deactivated successfully.');
    }
}
