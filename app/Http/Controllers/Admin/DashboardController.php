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
use App\Models\Task;

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

            // Task stats
            'total_tasks' => Task::count(),
            'active_tasks' => Task::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'overdue_tasks' => Task::where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
            'my_tasks' => Task::where('assigned_to', auth()->id())->count(),
            'tasks_due_today' => Task::where('status', '!=', 'Completed')->whereDate('due_date', now()->toDateString())->count(),
            'tasks_due_this_week' => Task::where('status', '!=', 'Completed')->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'critical_tasks' => Task::where('status', '!=', 'Completed')->where('priority', 'Critical')->count(),

            // Milestone stats
            'total_milestones' => \App\Models\Milestone::count(),
            'active_milestones' => \App\Models\Milestone::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_milestones' => \App\Models\Milestone::where('status', 'Completed')->count(),
        ];
        
        // Task Widgets Data
        $myAssignedTasks = Task::with('project')->where('assigned_to', auth()->id())->where('status', '!=', 'Completed')->latest()->take(5)->get();
        $recentlyCreatedTasks = Task::with('project')->latest()->take(5)->get();
        $recentlyCompletedTasks = Task::with('project')->where('status', 'Completed')->latest('completed_at')->take(5)->get();
        $overdueTasksList = Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->orderBy('due_date')->take(5)->get();
        $upcomingDeadlines = Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '>=', now())->orderBy('due_date')->take(5)->get();
        $tasksWaitingReview = Task::with('project')->where('status', 'Waiting Review')->latest()->take(5)->get();
        
        // Milestone Widgets Data
        $latestMilestones = \App\Models\Milestone::with('project')->latest()->take(5)->get();

        // Chart Placeholder Datasets
        $chartData = [
            'tasksByStatus' => [10, 25, 5, 40, 2],
            'tasksByPriority' => [15, 40, 20, 7],
            'tasksPerProject' => [12, 19, 3, 5, 2, 3],
            'monthlyTaskCompletion' => [65, 59, 80, 81, 56, 55, 40],
            'projectProgress' => [80, 45, 100, 20, 60],
        ];
        
        $latestProjects = Project::with('company')->latest()->take(5)->get();
        $latestClients = Client::latest()->take(5)->get();
        $latestTeamMembers = \App\Models\ProjectMember::with(['user', 'project', 'department'])->latest('joined_at')->take(5)->get();

        return view('admin.dashboard.index', compact(
            'stats', 'latestProjects', 'latestClients', 'latestTeamMembers', 
            'myAssignedTasks', 'recentlyCreatedTasks', 'recentlyCompletedTasks', 
            'overdueTasksList', 'upcomingDeadlines', 'tasksWaitingReview', 'chartData',
            'latestMilestones'
        ));
    }
}
