<?php

namespace App\Policies;

use App\Models\ProjectExpense;
use App\Models\Project;
use App\Models\User;

class ProjectExpensePolicy
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
     * Determine whether the user can view any project expenses.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('project_expense.view') || $user->hasPermissionTo('project.view-budget') || $user->hasPermissionTo('project.view');
    }

    /**
     * Determine whether the user can view the expense.
     */
    public function view(User $user, ProjectExpense $expense): bool
    {
        if ($expense->project) {
            return $expense->project->hasPermissionForUser($user, 'project_expense.view')
                || $expense->project->hasPermissionForUser($user, 'project.view-budget');
        }

        return $user->hasPermissionTo('project_expense.view') || $user->hasPermissionTo('project.view-budget');
    }

    /**
     * Determine whether the user can create project expenses.
     */
    public function create(User $user, ?Project $project = null): bool
    {
        if ($project) {
            return $project->hasPermissionForUser($user, 'project_expense.create')
                || $project->hasPermissionForUser($user, 'project.view-budget')
                || $project->hasPermissionForUser($user, 'project.update');
        }

        return $user->hasPermissionTo('project_expense.create') || $user->hasPermissionTo('project.view-budget');
    }

    /**
     * Determine whether the user can update the expense.
     */
    public function update(User $user, ProjectExpense $expense): bool
    {
        if ($expense->project) {
            if ($expense->project->status === 'Closed') {
                return false;
            }
            return $expense->project->hasPermissionForUser($user, 'project_expense.edit')
                || $expense->project->hasPermissionForUser($user, 'project.view-budget')
                || $expense->project->hasPermissionForUser($user, 'project.update');
        }

        return $user->hasPermissionTo('project_expense.edit') || $user->hasPermissionTo('project.view-budget');
    }

    /**
     * Determine whether the user can delete the expense.
     */
    public function delete(User $user, ProjectExpense $expense): bool
    {
        if ($expense->project) {
            if ($expense->project->status === 'Closed') {
                return false;
            }
            return $expense->project->hasPermissionForUser($user, 'project_expense.delete')
                || $expense->project->hasPermissionForUser($user, 'project.view-budget')
                || $expense->project->hasPermissionForUser($user, 'project.update');
        }

        return $user->hasPermissionTo('project_expense.delete') || $user->hasPermissionTo('project.view-budget');
    }
}
