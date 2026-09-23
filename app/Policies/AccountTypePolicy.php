<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\User;

class AccountTypePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO') || $user->hasRole('Administrator') || $user->hasRole('Company Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('account.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, AccountType $type): bool
    {
        if ($user->company_id && $type->company_id && $user->company_id !== $type->company_id) {
            return false;
        }

        return $user->hasPermissionTo('account.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('account.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, AccountType $type): bool
    {
        if ($user->company_id && $type->company_id && $user->company_id !== $type->company_id) {
            return false;
        }

        return $user->hasPermissionTo('account.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, AccountType $type): bool
    {
        if ($user->company_id && $type->company_id && $user->company_id !== $type->company_id) {
            return false;
        }

        return $user->hasPermissionTo('account.delete') || $user->hasPermissionTo('finance.delete');
    }
}
