<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('view-tasks');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-tasks');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('edit-tasks');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('delete-tasks');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('restore-tasks');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false; // Soft deletes only
    }

    /**
     * Determine whether the user can assign tasks.
     */
    public function assign(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('assign-tasks') || $user->hasPermissionTo('edit-tasks');
    }
}
