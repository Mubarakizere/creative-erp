<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use App\Models\Task;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\ProjectMember;
use App\Models\Milestone;
use App\Models\Comment;
use App\Models\Meeting;
use App\Services\CalendarService;

class DashboardService
{
    /**
     * Get all dashboard statistics and data.
     */
    public function getDashboardData(): array
    {
        $stats = $this->getGeneralStats();
        $widgets = $this->getWidgetsData();
        $charts = $this->getChartData();

        // Add calendar events to stats (from CalendarService)
        $calendarService = app(CalendarService::class);
        $userId = auth()->id();
        $companyId = auth()->user()->hasRole('Super Admin') ? null : auth()->user()->company_id;
        
        $stats['events_this_week'] = $calendarService->getWeekEvents($userId, $companyId)->count();
        $widgets['todaysSchedule'] = $calendarService->getTodaysSchedule($userId, $companyId);
        $widgets['upcomingMeetings'] = Meeting::upcoming()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->forUser($userId)
            ->take(5)
            ->get();
        $charts = $this->getChartData();

        return array_merge(['stats' => $stats, 'chartData' => $charts], $widgets);
    }

    /**
     * Get general statistics for dashboard cards.
     */
    private function getGeneralStats(): array
    {
        return [
            // Organization Stats
            'clients' => Client::count(),
            'projects' => Project::count(),
            'companies' => Company::count(),
            'branches' => Branch::count(),
            'departments' => Department::count(),
            'users' => User::count(),
            
            // Project Stats
            'active_projects' => Project::whereIn('status', ['Planning', 'Pending', 'In Progress'])->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'on_hold_projects' => Project::where('status', 'On Hold')->count(),
            'closed_projects' => Project::where('status', 'Closed')->count(),
            'total_estimated_budget' => Project::sum('estimated_budget'),
            'total_actual_budget' => Project::sum('actual_budget'),
            
            // Team Stats
            'total_team_members' => ProjectMember::count(),
            'active_team_members' => ProjectMember::where('status', 'Active')->count(),
            'inactive_team_members' => ProjectMember::where('status', 'Inactive')->count(),
            'project_managers' => ProjectMember::where('project_role', 'Project Manager')->where('status', 'Active')->count(),
            'engineers' => ProjectMember::where('project_role', 'like', '%Engineer%')->count(),

            // Task Stats
            'total_tasks' => Task::count(),
            'active_tasks' => Task::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_tasks' => Task::where('status', 'Completed')->count(),
            'overdue_tasks' => Task::where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
            'my_tasks' => Task::where('assigned_to', auth()->id())->count(),
            'tasks_due_today' => Task::where('status', '!=', 'Completed')->whereDate('due_date', now()->toDateString())->count(),
            'tasks_due_this_week' => Task::where('status', '!=', 'Completed')->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'critical_tasks' => Task::where('status', '!=', 'Completed')->where('priority', 'Critical')->count(),

            // Milestone Stats
            'total_milestones' => Milestone::count(),
            'active_milestones' => Milestone::whereIn('status', ['Pending', 'In Progress'])->count(),
            'completed_milestones' => Milestone::where('status', 'Completed')->count(),
            
            // Document Stats
            'total_documents' => Document::count(),
            'document_categories' => DocumentCategory::count(),

            // Discussion Stats
            'total_discussions' => Comment::whereNull('parent_id')->count(),
            'comments_today' => Comment::whereDate('created_at', now())->count(),
            'my_mentions' => Comment::where('body', 'like', '%@' . auth()->user()->first_name . '%')->count(),
            'active_threads' => Comment::whereNull('parent_id')->has('replies')->count(),
            'internal_notes' => Comment::where('is_internal', true)->count(),

            // Meeting Stats
            'meetings_today' => Meeting::today()->count(),
            'upcoming_meetings' => Meeting::upcoming()->count(),
            'schedule_conflicts' => 0, // Calculated dynamically when needed
        ];
    }

    /**
     * Get recent and upcoming records for widgets.
     */
    private function getWidgetsData(): array
    {
        return [
            // Tasks
            'myAssignedTasks' => Task::with('project')->where('assigned_to', auth()->id())->where('status', '!=', 'Completed')->latest()->take(5)->get(),
            'recentlyCreatedTasks' => Task::with('project')->latest()->take(5)->get(),
            'recentlyCompletedTasks' => Task::with('project')->where('status', 'Completed')->latest('completed_at')->take(5)->get(),
            'overdueTasksList' => Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '<', now())->orderBy('due_date')->take(5)->get(),
            'upcomingDeadlines' => Task::with('project')->where('status', '!=', 'Completed')->whereNotNull('due_date')->where('due_date', '>=', now())->orderBy('due_date')->take(5)->get(),
            'tasksWaitingReview' => Task::with('project')->where('status', 'Waiting Review')->latest()->take(5)->get(),
            
            // Milestones & Documents
            'latestMilestones' => Milestone::with('project')->latest()->take(5)->get(),
            'latestDocuments' => Document::with('documentable')->latest()->take(5)->get(),

            // Discussions
            'recentDiscussions' => Comment::with(['commentable', 'user'])->whereNull('parent_id')->latest()->take(5)->get(),
            'latestReplies' => Comment::with(['commentable', 'user'])->whereNotNull('parent_id')->latest()->take(5)->get(),
            'myMentions' => Comment::with(['commentable', 'user'])->where('body', 'like', '%@' . auth()->user()->first_name . '%')->latest()->take(5)->get(),
            'recentlyPinned' => Comment::with(['commentable', 'user'])->where('is_pinned', true)->latest()->take(5)->get(),

            // General
            'latestProjects' => Project::with('company')->latest()->take(5)->get(),
            'latestClients' => Client::with('company')->latest()->take(5)->get(),
            'latestTeamMembers' => ProjectMember::with(['user', 'project', 'department'])->latest('joined_at')->take(5)->get(),
        ];
    }

    /**
     * Get chart data (replacing placeholders with real queries where possible).
     */
    private function getChartData(): array
    {
        // Using real DB data for some charts where easily queried
        return [
            'tasksByStatus' => [
                Task::where('status', 'Pending')->count(),
                Task::where('status', 'In Progress')->count(),
                Task::where('status', 'Waiting Review')->count(),
                Task::where('status', 'Completed')->count(),
                Task::where('status', 'On Hold')->count(),
            ],
            'tasksByPriority' => [
                Task::where('priority', 'Low')->count(),
                Task::where('priority', 'Medium')->count(),
                Task::where('priority', 'High')->count(),
                Task::where('priority', 'Critical')->count(),
            ],
            'projectProgress' => Project::whereIn('status', ['Planning', 'In Progress'])->take(5)->pluck('progress')->toArray() ?: [0],
            
            // Keep placeholders for complex historical timeline data for now
            'tasksPerProject' => [12, 19, 3, 5, 2, 3],
            'monthlyTaskCompletion' => [65, 59, 80, 81, 56, 55, 40],
            'commentsPerModule' => [30, 40, 15, 15],
            'commentsPerUser' => [12, 19, 14, 5, 2],
            'dailyDiscussions' => [5, 10, 15, 8, 12, 20, 25],
            'monthlyDiscussions' => [50, 60, 45, 70, 90, 80],
            'mentionsPerMonth' => [10, 15, 5, 20, 25, 30],
            
            // Meeting Charts (Placeholders)
            'meetingsPerMonth' => [4, 8, 15, 12, 20, 18, 25],
            'meetingsByType' => [10, 5, 8, 3, 2, 4],
            'attendanceRate' => [95, 92, 88, 96, 90],
        ];
    }
}
