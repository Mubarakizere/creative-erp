<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\Client;
use App\Models\Project;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index(): View
    {
        $stats = [
            'clients' => Client::count(),
            'projects' => Project::count(),
            'companies' => Company::count(),
            'branches' => Branch::count(),
            'departments' => Department::count(),
            'users' => User::count(),
            
            'active_projects' => Project::whereIn('status', ['Planning', 'Pending', 'In Progress'])->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'on_hold_projects' => Project::where('status', 'On Hold')->count(),
            'closed_projects' => Project::where('status', 'Closed')->count(),
            
            'total_estimated_budget' => Project::sum('estimated_budget'),
            'total_actual_budget' => Project::sum('actual_budget'),
            
            // Team stats
            'total_team_members' => \App\Models\ProjectMember::count(),
            'active_team_members' => \App\Models\ProjectMember::where('status', 'Active')->count(),
            'inactive_team_members' => \App\Models\ProjectMember::where('status', 'Inactive')->count(),
            'project_managers' => \App\Models\ProjectMember::where('project_role', 'Project Manager')->where('status', 'Active')->count(),
            'engineers' => \App\Models\ProjectMember::where('project_role', 'like', '%Engineer%')->count(),
        ];
        
        $latestProjects = Project::with('company')->latest()->take(5)->get();
        $latestClients = Client::latest()->take(5)->get();
        $latestTeamMembers = \App\Models\ProjectMember::with(['user', 'project', 'department'])->latest('joined_at')->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'latestProjects', 'latestClients', 'latestTeamMembers'));
    }
}
