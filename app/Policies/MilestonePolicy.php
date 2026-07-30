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
        return $user->hasPermissionTo('milestone.view');
    }

    public function view(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('milestone.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('milestone.create');
    }

    public function update(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('milestone.update');
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('milestone.delete');
    }

    public function restore(User $user, Milestone $milestone): bool
    {
        if ($user->company_id !== $milestone->company_id) {
            return false;
        }
        return $user->hasPermissionTo('milestone.restore');
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
        return $user->hasPermissionTo('milestone.update');
    }
}
