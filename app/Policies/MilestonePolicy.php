<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MilestonePolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-milestones');
    }

    public function view(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('view-milestones');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-milestones');
    }

    public function update(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('edit-milestones');
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('delete-milestones');
    }

    public function restore(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('restore-milestones');
    }

    public function forceDelete(User $user, Milestone $milestone): bool
    {
        return false;
    }

    public function assignTasks(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('edit-milestones');
    }
}
