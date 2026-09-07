<?php

namespace App\Policies;

use App\Models\ProjectMaterialIssue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectMaterialIssuePolicy
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

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('material_issue.view')
            || $user->hasPermissionTo('material_request.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectMaterialIssue $issue): bool
    {
        if ($issue->project) {
            return $issue->project->hasPermissionForUser($user, 'material_issue.view')
                || $issue->project->hasPermissionForUser($user, 'material_request.view');
        }

        return $user->hasPermissionTo('material_issue.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('material_issue.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectMaterialIssue $issue): bool
    {
        if ($issue->status !== 'Pending') {
            return false;
        }

        if ($issue->project) {
            return $issue->project->hasPermissionForUser($user, 'material_issue.update');
        }

        return $user->hasPermissionTo('material_issue.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectMaterialIssue $issue): bool
    {
        if ($issue->status !== 'Pending') {
            return false;
        }

        if ($issue->project) {
            return $issue->project->hasPermissionForUser($user, 'material_issue.delete');
        }

        return $user->hasPermissionTo('material_issue.delete');
    }
}
