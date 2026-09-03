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
        return $project->hasPermissionForUser($user, 'project.view');
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

        return $project->hasPermissionForUser($user, 'project.update');
    }

    public function delete(User $user, Project $project): bool
    {
        if ($project->status === 'Closed') {
            return false;
        }

        return $project->hasPermissionForUser($user, 'project.delete') || $project->hasPermissionForUser($user, 'project.archive');
    }

    public function restore(User $user, Project $project): bool
    {
        return $project->hasPermissionForUser($user, 'project.restore');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        // Business Rule: Projects with financial records cannot be permanently deleted.
        if ($project->actual_budget > 0 || $project->actual_cost > 0) {
            return false;
        }

        return $project->hasPermissionForUser($user, 'project.delete');
    }
    
    public function close(User $user, Project $project): bool
    {
        return $project->hasPermissionForUser($user, 'project.close') || $project->hasPermissionForUser($user, 'project.update');
    }
    
    public function reopen(User $user, Project $project): bool
    {
        return $project->hasPermissionForUser($user, 'project.reopen') || $project->hasPermissionForUser($user, 'project.update');
    }
}
