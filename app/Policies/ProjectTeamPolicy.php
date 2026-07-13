<?php

namespace App\Policies;

use App\Models\ProjectMember;
use App\Models\User;

class ProjectTeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('project-team.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('project-team.create');
    }
    
    /**
     * Determine whether the user can assign members.
     */
    public function assign(User $user): bool
    {
        return $user->can('project-team.assign');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.delete');
    }
    
    /**
     * Determine whether the user can remove the model (soft delete).
     */
    public function remove(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.remove');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProjectMember $projectMember): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can activate the model.
     */
    public function activate(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.activate');
    }
    
    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $user, ProjectMember $projectMember): bool
    {
        return $user->can('project-team.deactivate');
    }
}
