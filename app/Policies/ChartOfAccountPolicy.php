<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChartOfAccountPolicy
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
        return $user->hasPermissionTo('account.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, ChartOfAccount $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('account.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('account.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, ChartOfAccount $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('account.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, ChartOfAccount $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return !$account->is_system && ($user->hasPermissionTo('account.delete') || $user->hasPermissionTo('finance.delete'));
    }
}
