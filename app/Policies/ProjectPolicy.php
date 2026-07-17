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
        if ($user->company_id && $user->company_id !== $project->company_id) {
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
        // Closed projects are read-only (Super Admin bypasses via Gate::before)
        if ($project->status === 'Closed') {
            return false;
        }

        if ($user->company_id && $user->company_id !== $project->company_id) {
            return false;
        }

        return $user->hasPermissionTo('project.update');
    }

    public function delete(User $user, Project $project): bool
    {
        if ($project->status === 'Closed') {
            return false;
        }

        if ($user->company_id && $user->company_id !== $project->company_id) {
            return false;
        }

        return $user->hasPermissionTo('project.delete') || $user->hasPermissionTo('project.archive');
    }

    public function restore(User $user, Project $project): bool
    {
        if ($user->company_id && $user->company_id !== $project->company_id) {
            return false;
        }

        return $user->hasPermissionTo('project.restore');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        // Business Rule: Projects with financial records cannot be permanently deleted.
        if ($project->actual_budget > 0 || $project->actual_cost > 0) {
            return false;
        }

        return $user->hasPermissionTo('project.delete');
    }
    
    public function close(User $user, Project $project): bool
    {
        if ($user->company_id && $user->company_id !== $project->company_id) {
            return false;
        }

        return $user->hasPermissionTo('project.close');
    }
    
    public function reopen(User $user, Project $project): bool
    {
        if ($user->company_id && $user->company_id !== $project->company_id) {
            return false;
        }

        return $user->hasPermissionTo('project.reopen');
    }
}
