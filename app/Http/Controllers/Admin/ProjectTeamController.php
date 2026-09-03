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
use Spatie\Permission\Models\Role;
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
     * Helper to get merged project roles (DB System Roles + Operational Roles)
     */
    protected function getProjectRoles(): array
    {
        $systemRoles = Role::orderBy('name')->pluck('name', 'name')->toArray();
        $operationalRoles = [
            'Assistant Project Manager' => 'Assistant Project Manager',
            'Architect' => 'Architect',
            'Civil Engineer' => 'Civil Engineer',
            'Electrical Engineer' => 'Electrical Engineer',
            'Mechanical Engineer' => 'Mechanical Engineer',
            'Quantity Surveyor' => 'Quantity Surveyor',
            'Quality Controller' => 'Quality Controller',
            'Safety Officer' => 'Safety Officer',
            'Foreman' => 'Foreman',
            'Technician' => 'Technician',
            'Viewer' => 'Viewer',
        ];

        $roles = array_merge($systemRoles, $operationalRoles);
        ksort($roles);
        return $roles;
    }

    /**
     * Display a listing of project teams across all projects.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectMember::class);

        $viewMode = $request->get('view', 'projects'); // 'projects' or 'members'

        // Grouped Project Teams Query
        $projectTeamsQuery = Project::with(['manager', 'projectMembers' => function ($q) {
            $q->with(['user', 'department'])->whereNull('deleted_at');
        }])->where('company_id', auth()->user()->company_id);

        if ($request->search) {
            $search = $request->search;
            $projectTeamsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhereHas('projectMembers.user', function ($sub) use ($search) {
                      $sub->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->project_id) {
            $projectTeamsQuery->where('id', $request->project_id);
        }

        $projectTeams = $projectTeamsQuery->orderBy('name')->paginate(12)->withQueryString();

        // Flat Members Query
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

        return view('admin.projects.team.index', compact('members', 'projectTeams', 'projects', 'departments', 'viewMode'));
    }

    /**
     * Show the form for creating a new project member (assigning a user to a project).
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', ProjectMember::class);
        
        $projects = Project::select('id', 'name')->orderBy('name')->get();
        $users = User::with('roles')->select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $usersMap = $users->mapWithKeys(function($u) {
            return [$u->id => [
                'name' => $u->first_name . ' ' . $u->last_name,
                'role' => $u->roles->first()?->name ?? ''
            ]];
        });
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        $roles = $this->getProjectRoles();
        
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('admin.projects.team.create', compact('projects', 'users', 'usersMap', 'departments', 'roles', 'selectedProject'));
    }

    /**
     * Store a newly created project member in storage.
     */
    public function store(StoreProjectMemberRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->project_id);
        
        try {
            $this->teamService->assignMember($project, $request->validated());
            return redirect()->route('admin.projects.team.index')->with('success', 'Team member assigned successfully.');
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
        $roles = $this->getProjectRoles();
        $teamMember->load(['project', 'user']);

        return view('admin.projects.team.edit', compact('teamMember', 'departments', 'roles'));
    }

    /**
     * Update the specified project member in storage.
     */
    public function update(UpdateProjectMemberRequest $request, ProjectMember $teamMember): RedirectResponse
    {
        try {
            $this->teamService->updateAssignment($teamMember, $request->validated());
            return redirect()->route('admin.projects.team.index')->with('success', 'Team member updated successfully.');
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
        
        $this->teamService->removeMember($teamMember);
        
        return redirect()->route('admin.projects.team.index')->with('success', 'Team member removed successfully.');
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
