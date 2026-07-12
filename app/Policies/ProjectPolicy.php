<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('project.view');
    }

    public function view(User $user, Project $project): bool
    {
        // Add company restriction if user is not Super Admin
        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('project.create');
    }

    public function update(User $user, Project $project): bool
    {
        // Closed projects are read-only except for Super Admin
        if ($project->status === 'Closed' && !$user->hasRole('Super Admin')) {
            return false;
        }

        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.update');
    }

    public function delete(User $user, Project $project): bool
    {
        if ($project->status === 'Closed' && !$user->hasRole('Super Admin')) {
            return false;
        }

        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.delete') || $user->hasPermissionTo('project.archive');
    }

    public function restore(User $user, Project $project): bool
    {
        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.restore');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        // Business Rule: Projects with financial records cannot be permanently deleted.
        // For now, only Super Admin can force delete, and maybe we check if actual_budget/cost is set.
        if (!$user->hasRole('Super Admin')) {
            return false;
        }

        if ($project->actual_budget > 0 || $project->actual_cost > 0) {
            return false;
        }

        return $user->hasPermissionTo('project.delete');
    }
    
    public function close(User $user, Project $project): bool
    {
        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.close');
    }
    
    public function reopen(User $user, Project $project): bool
    {
        if (!$user->hasRole('Super Admin') && !$user->companies->contains('id', $project->company_id)) {
            return false;
        }

        return $user->hasPermissionTo('project.reopen');
    }
}
