<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Project;
use App\Models\ProjectMember;

class ProjectMetrics implements MetricProvider
{
    protected ?string $companyId;
    protected ?int $userId;

    public function __construct()
    {
        $this->userId = auth()->id();
        $this->companyId = auth()->user()?->company_id;
    }

    public function cards(): array
    {
        if (!auth()->user()?->can('project.view')) {
            return [];
        }

        $projectQuery = Project::query();
        if ($this->companyId) {
            $projectQuery->where('company_id', $this->companyId);
        }

        $memberQuery = ProjectMember::query();
        if ($this->companyId) {
            $memberQuery->whereHas('project', function ($q) {
                $q->where('company_id', $this->companyId);
            });
        }

        return [
            'projects' => (clone $projectQuery)->count(),
            'active_projects' => (clone $projectQuery)->whereIn('status', ['Planning', 'Pending', 'In Progress'])->count(),
            'completed_projects' => (clone $projectQuery)->where('status', 'Completed')->count(),
            'on_hold_projects' => (clone $projectQuery)->where('status', 'On Hold')->count(),
            'closed_projects' => (clone $projectQuery)->where('status', 'Closed')->count(),
            'total_estimated_budget' => auth()->user()?->can('project.view-budget') ? (clone $projectQuery)->sum('estimated_budget') : 0,
            'total_actual_budget' => auth()->user()?->can('project.view-budget') ? (clone $projectQuery)->sum('actual_budget') : 0,
            
            // Team Stats
            'total_team_members' => (clone $memberQuery)->count(),
            'active_team_members' => (clone $memberQuery)->where('status', 'Active')->count(),
            'inactive_team_members' => (clone $memberQuery)->where('status', 'Inactive')->count(),
            'project_managers' => (clone $memberQuery)->where('project_role', 'Project Manager')->where('status', 'Active')->count(),
            'engineers' => (clone $memberQuery)->where('project_role', 'like', '%Engineer%')->count(),
        ];
    }

    public function widgets(): array
    {
        if (!auth()->user()?->can('project.view')) {
            return [];
        }

        $projectQuery = Project::with('company');
        if ($this->companyId) {
            $projectQuery->where('company_id', $this->companyId);
        }

        $memberQuery = ProjectMember::with(['user', 'project', 'department']);
        if ($this->companyId) {
            $memberQuery->whereHas('project', function ($q) {
                $q->where('company_id', $this->companyId);
            });
        }

        return [
            'latestProjects' => $projectQuery->latest()->take(5)->get(),
            'latestTeamMembers' => auth()->user()?->can('project-team.view') ? $memberQuery->latest('joined_at')->take(5)->get() : collect([]),
        ];
    }

    public function reports(): array
    {
        return [];
    }
}
