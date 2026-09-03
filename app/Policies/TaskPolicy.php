<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

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
        return $user->hasPermissionTo('project_task.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        if ($task->project) {
            return $task->project->hasPermissionForUser($user, 'project_task.view');
        }
        return $user->hasPermissionTo('project_task.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Project $project = null): bool
    {
        if ($project) {
            return $project->hasPermissionForUser($user, 'project_task.create');
        }
        return $user->hasPermissionTo('project_task.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($task->project) {
            return $task->project->hasPermissionForUser($user, 'project_task.update');
        }
        return $user->hasPermissionTo('project_task.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($task->project) {
            return $task->project->hasPermissionForUser($user, 'project_task.delete');
        }
        return $user->hasPermissionTo('project_task.delete');
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
