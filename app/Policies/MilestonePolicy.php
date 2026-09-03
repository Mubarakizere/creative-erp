<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MilestonePolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

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

    protected function resolveProject(mixed $arg1, mixed $arg2): ?\App\Models\Project
    {
        if ($arg1 instanceof \App\Models\Project) {
            return $arg1;
        }
        if ($arg2 instanceof \App\Models\Project) {
            return $arg2;
        }
        if ($arg1 instanceof Milestone && $arg1->project) {
            return $arg1->project;
        }
        return null;
    }

    public function viewAny(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'milestone.view');
        }
        return $user->hasPermissionTo('milestone.view');
    }

    public function view(User $user, Milestone $milestone): bool
    {
        if ($milestone->project) {
            return $milestone->project->hasPermissionForUser($user, 'milestone.view');
        }
        return $user->hasPermissionTo('milestone.view');
    }

    public function create(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $this->resolveProject($arg1, $arg2);
        if ($project) {
            return $project->hasPermissionForUser($user, 'milestone.create');
        }
        return $user->hasPermissionTo('milestone.create');
    }

    public function update(User $user, Milestone $milestone): bool
    {
        if ($milestone->project) {
            return $milestone->project->hasPermissionForUser($user, 'milestone.update');
        }
        return $user->hasPermissionTo('milestone.update');
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        if ($milestone->project) {
            return $milestone->project->hasPermissionForUser($user, 'milestone.delete');
        }
        return $user->hasPermissionTo('milestone.delete');
    }

    public function restore(User $user, Milestone $milestone): bool
    {
        if ($user->company_id && $user->company_id !== $milestone->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('milestone.restore')) {
            return false;
        }

        if ($milestone->project && !$milestone->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    public function forceDelete(User $user, Milestone $milestone): bool
    {
        return false;
    }

    public function assignTasks(User $user, Milestone $milestone): bool
    {
        if ($user->company_id && $user->company_id !== $milestone->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('milestone.update')) {
            return false;
        }

        if ($milestone->project && !$milestone->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }
}
