<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
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
        return $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('account.view');
    }

    public function view(User $user, Account $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->id === $account->owner_id || $user->id === $account->created_by || $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('account.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create') || $user->hasPermissionTo('account.create');
    }

    public function update(User $user, Account $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->id === $account->owner_id || $user->id === $account->created_by || $user->hasPermissionTo('crm.update') || $user->hasPermissionTo('account.update');
    }

    public function delete(User $user, Account $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        // Prevent deleting accounts with open opportunities
        if ($account->opportunities()->where('status', 'Open')->exists()) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete') || $user->hasPermissionTo('account.delete');
    }

    public function restore(User $user, Account $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Account $account): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $account->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
