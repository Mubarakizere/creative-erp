<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

class ProjectTeamPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return null;
    }

    protected function resolveProject(mixed $arg1, mixed $arg2): ?Project
    {
        if ($arg1 instanceof Project) {
            return $arg1;
        }
        if ($arg2 instanceof Project) {
            return $arg2;
        }
        if ($arg1 instanceof ProjectMember) {
            return $arg1->project;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'project.view');
        }
        return $user->hasPermissionTo('project.view') || $user->hasPermissionTo('project_task.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.view');
        }
        return $user->hasPermissionTo('project.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'project.update') || $project->hasPermissionForUser($user, 'project.create');
        }
        return $user->hasPermissionTo('project.update') || $user->hasPermissionTo('project.create');
    }
    
    /**
     * Determine whether the user can assign members.
     */
    public function assign(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'project.update') || $project->hasPermissionForUser($user, 'project.create');
        }
        return $user->hasPermissionTo('project.update') || $user->hasPermissionTo('project.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update');
        }
        return $user->hasPermissionTo('project.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update') || $projectMember->project->hasPermissionForUser($user, 'project.delete');
        }
        return $user->hasPermissionTo('project.update') || $user->hasPermissionTo('project.delete');
    }
    
    /**
     * Determine whether the user can remove the model (soft delete).
     */
    public function remove(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update') || $projectMember->project->hasPermissionForUser($user, 'project.delete');
        }
        return $user->hasPermissionTo('project.update') || $user->hasPermissionTo('project.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update');
        }
        return $user->hasPermissionTo('project.update');
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
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update');
        }
        return $user->hasPermissionTo('project.update');
    }
    
    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $user, ProjectMember $projectMember): bool
    {
        if ($projectMember->project) {
            return $projectMember->project->hasPermissionForUser($user, 'project.update');
        }
        return $user->hasPermissionTo('project.update');
    }
}
