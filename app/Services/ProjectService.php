<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProjectService
{
    /**
     * Get a paginated list of projects with optional filters.
     *
     * @param array<string, mixed> $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Project::with(['company', 'branch', 'client', 'manager']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['trashed']) && $filters['trashed'] == 1) {
            $query->onlyTrashed();
        }

        return $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
    }

    /**
     * Create a new project.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Project
    {
        $data['created_by'] = auth()->id();
        return Project::create($data);
    }

    /**
     * Update an existing project.
     *
     * @param array<string, mixed> $data
     */
    public function update(Project $project, array $data): Project
    {
        $data['updated_by'] = auth()->id();
        $project->update($data);

        return $project->fresh();
    }

    /**
     * Soft delete a project (Archive).
     */
    public function delete(Project $project): bool
    {
        // Business Rule: Projects with financial records cannot be permanently deleted.
        // We are using soft deletes, so it's archived.
        return (bool) $project->delete();
    }

    /**
     * Restore an archived project.
     */
    public function restore(Project $project): bool
    {
        return $project->restore();
    }

    /**
     * Close a project.
     */
    public function close(Project $project): bool
    {
        return $project->update([
            'status' => 'Closed',
            'progress' => 100,
            'actual_end_date' => now(),
            'updated_by' => auth()->id()
        ]);
    }

    /**
     * Reopen a closed project.
     */
    public function reopen(Project $project): bool
    {
        return $project->update([
            'status' => 'In Progress',
            'actual_end_date' => null,
            'updated_by' => auth()->id()
        ]);
    }
    
    /**
     * Duplicate an existing project.
     */
    public function duplicate(Project $project): Project
    {
        $newProject = $project->replicate();
        
        $newProject->uuid = (string) Str::uuid();
        $newProject->project_code = $this->generateUniqueProjectCode($project->company_id);
        $newProject->name = $project->name . ' (Copy)';
        $newProject->status = 'Planning';
        $newProject->progress = 0;
        $newProject->actual_budget = null;
        $newProject->actual_cost = null;
        $newProject->actual_end_date = null;
        $newProject->created_by = auth()->id();
        $newProject->updated_by = null;
        $newProject->created_at = now();
        $newProject->updated_at = now();
        
        $newProject->save();
        
        return $newProject;
    }

    /**
     * Generate a unique project code for a company.
     */
    public function generateUniqueProjectCode(int $companyId): string
    {
        do {
            $code = 'PRJ-' . strtoupper(Str::random(8));
        } while (Project::where('company_id', $companyId)->where('project_code', $code)->exists());

        return $code;
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'total' => Project::count(),
            'active' => Project::whereIn('status', ['Planning', 'Pending', 'In Progress'])->count(),
            'completed' => Project::where('status', 'Completed')->count(),
            'on_hold' => Project::where('status', 'On Hold')->count(),
            'closed' => Project::where('status', 'Closed')->count(),
            'total_estimated_budget' => Project::sum('estimated_budget'),
            'total_actual_budget' => Project::sum('actual_budget'),
        ];
    }
}
