<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Project;
use App\Models\ProjectMember;

class ProjectMetrics implements MetricProvider
{
    use FiltersMetrics;

    protected ?string $companyId;
    protected ?int $userId;

    public function __construct()
    {
        $this->userId = auth()->id();
        $this->companyId = auth()->user()?->company_id;
    }

    public function cards(array $filters = []): array
    {
        $user = auth()->user();
        if (!$user?->can('project.view')) {
            return [];
        }

        $projectQuery = $this->applyFilters(Project::query(), $filters);
        if ($this->companyId) {
            $projectQuery->where('company_id', $this->companyId);
        }
        $projectQuery->accessibleBy($user);

        $memberQuery = ProjectMember::query();
        if ($this->companyId) {
            $memberQuery->whereHas('project', function ($q) use ($user) {
                $q->where('company_id', $this->companyId)->accessibleBy($user);
            });
        } else {
            $memberQuery->whereHas('project', function ($q) use ($user) {
                $q->accessibleBy($user);
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
            'total_actual_cost' => auth()->user()?->can('project.view-budget') ? (clone $projectQuery)->sum('actual_cost') : 0,
            'remaining_budget' => auth()->user()?->can('project.view-budget') ? (clone $projectQuery)->sum('actual_budget') - (clone $projectQuery)->sum('actual_cost') : 0,
            'budget_utilization_percent' => auth()->user()?->can('project.view-budget') && (clone $projectQuery)->sum('actual_budget') > 0 ? ((clone $projectQuery)->sum('actual_cost') / (clone $projectQuery)->sum('actual_budget')) * 100 : 0,
            'total_material_cost' => auth()->user()?->can('project.view-budget') ? (clone $projectQuery)->withSum('tasks', 'actual_material_cost')->get()->sum('tasks_sum_actual_material_cost') : 0,
            'total_equipment_cost' => 0, // Pulled from GL or AssetAssignment if applicable
            'total_procurement_cost' => 0, // Pulled from POs or GL if applicable
            
            // Team Stats
            'total_team_members' => (clone $memberQuery)->count(),
            'active_team_members' => (clone $memberQuery)->where('status', 'Active')->count(),
            'inactive_team_members' => (clone $memberQuery)->where('status', 'Inactive')->count(),
            'project_managers' => (clone $memberQuery)->where('project_role', 'Project Manager')->where('status', 'Active')->count(),
            'engineers' => (clone $memberQuery)->where('project_role', 'like', '%Engineer%')->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        $user = auth()->user();
        if (!$user?->can('project.view')) {
            return [];
        }

        $projectQuery = $this->applyFilters(Project::query(), $filters)->with('company');
        if ($this->companyId) {
            $projectQuery->where('company_id', $this->companyId);
        }
        $projectQuery->accessibleBy($user);

        $memberQuery = ProjectMember::with(['user', 'project', 'department']);
        if ($this->companyId) {
            $memberQuery->whereHas('project', function ($q) use ($user) {
                $q->where('company_id', $this->companyId)->accessibleBy($user);
            });
        } else {
            $memberQuery->whereHas('project', function ($q) use ($user) {
                $q->accessibleBy($user);
            });
        }

        $projects = (clone $projectQuery)->get();
        $projectsApproachingBudget = collect();
        $projectsOverBudget = collect();
        
        foreach ($projects as $p) {
            if ($p->actual_budget > 0) {
                $utilization = ($p->actual_cost / $p->actual_budget) * 100;
                if ($utilization >= 80 && $utilization <= 100) {
                    $projectsApproachingBudget->push($p);
                } elseif ($utilization > 100) {
                    $projectsOverBudget->push($p);
                }
            }
        }

        return [
            'latestProjects' => (clone $projectQuery)->latest()->take(5)->get(),
            'latestTeamMembers' => auth()->user()?->can('project-team.view') ? $memberQuery->latest('joined_at')->take(5)->get() : collect([]),
            'topCostProjects' => (clone $projectQuery)->orderBy('actual_cost', 'desc')->take(5)->get(),
            'projectsApproachingBudget' => $projectsApproachingBudget->sortByDesc('actual_cost')->take(5)->values(),
            'projectsOverBudget' => $projectsOverBudget->sortByDesc('actual_cost')->take(5)->values(),
            'topMaterialCostActivities' => \App\Models\Task::whereIn('project_id', (clone $projectQuery)->pluck('id'))->orderBy('actual_material_cost', 'desc')->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            'projectProfitability' => $this->projectProfitability($filters)
        ];
    }

    public function projectProfitability(array $filters = []): array
    {
        $companyId = $filters['company_id'] ?? (auth()->user() ? auth()->user()->company_id : null);
        if (!$companyId) return [];

        $projects = Project::where('company_id', $companyId)->get();
        
        $profitability = [];
        foreach ($projects as $project) {
            $revenue = \App\Models\Invoice::where('project_id', $project->id)
                ->where('status', '!=', 'Cancelled')
                ->where('status', '!=', 'Voided')
                ->sum('total_amount');

            if ($revenue > 0) {
                $profitability[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'revenue' => (float) $revenue,
                    'expenses' => 0,
                    'net_profit' => (float) $revenue
                ];
            }
        }

        return collect($profitability)->sortByDesc('net_profit')->values()->toArray();
    }
}
