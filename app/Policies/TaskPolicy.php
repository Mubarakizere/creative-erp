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
        return $user->hasPermissionTo('project_task.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->company_id && $user->company_id !== $task->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('project_task.view')) {
            return false;
        }

        if ($task->project && !$task->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('project_task.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->company_id && $user->company_id !== $task->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('project_task.update')) {
            return false;
        }

        if ($task->project && !$task->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->company_id && $user->company_id !== $task->company_id) {
            return false;
        }

        if (!$user->hasPermissionTo('project_task.delete')) {
            return false;
        }

        if ($task->project && !$task->project->isAssignedTo($user)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        if ($user->company_id !== $task->company_id) {
            return false;
        }
        return $user->hasPermissionTo('project_task.restore');
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
        return $user->hasPermissionTo('project_task.update') || $user->hasPermissionTo('project_task.update');
    }
}
