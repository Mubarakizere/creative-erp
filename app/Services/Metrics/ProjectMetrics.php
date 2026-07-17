<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Project;
use App\Models\ProjectMember;

class ProjectMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'projects' => Project::count(),
            'active_projects' => Project::whereIn('status', ['Planning', 'Pending', 'In Progress'])->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'on_hold_projects' => Project::where('status', 'On Hold')->count(),
            'closed_projects' => Project::where('status', 'Closed')->count(),
            'total_estimated_budget' => Project::sum('estimated_budget'),
            'total_actual_budget' => Project::sum('actual_budget'),
            
            // Team Stats included in ProjectMetrics since they relate to ProjectMember
            'total_team_members' => ProjectMember::count(),
            'active_team_members' => ProjectMember::where('status', 'Active')->count(),
            'inactive_team_members' => ProjectMember::where('status', 'Inactive')->count(),
            'project_managers' => ProjectMember::where('project_role', 'Project Manager')->where('status', 'Active')->count(),
            'engineers' => ProjectMember::where('project_role', 'like', '%Engineer%')->count(),
        ];
    }

    public function widgets(): array
    {
        return [
            'latestProjects' => Project::with('company')->latest()->take(5)->get(),
            'latestTeamMembers' => ProjectMember::with(['user', 'project', 'department'])->latest('joined_at')->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [
            // Project Summary data
        ];
    }
}
