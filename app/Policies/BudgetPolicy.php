<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetPolicy
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

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('budget.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, Budget $budget): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $budget->company_id && $user->company_id !== $budget->company_id) {
            return false;
        }

        return $user->hasPermissionTo('budget.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('budget.manage') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, Budget $budget): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $budget->company_id && $user->company_id !== $budget->company_id) {
            return false;
        }

        return $user->hasPermissionTo('budget.manage') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, Budget $budget): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $budget->company_id && $user->company_id !== $budget->company_id) {
            return false;
        }

        return $user->hasPermissionTo('budget.manage') || $user->hasPermissionTo('finance.delete');
    }

    public function restore(User $user, Budget $budget): bool
    {
        return $user->can('budget.manage') && $user->company_id === $budget->company_id;
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return false; // Typically don't allow force delete for financial records
    }
}
