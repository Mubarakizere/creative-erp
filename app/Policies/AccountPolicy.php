<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Account $account): bool
    {
        if ($user->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Account $account): bool
    {
        if ($user->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Account $account): bool
    {
        if ($user->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        // Prevent deleting accounts with open opportunities
        if ($account->opportunities()->where('status', 'Open')->exists()) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete');
    }

    public function restore(User $user, Account $account): bool
    {
        if ($user->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Account $account): bool
    {
        if ($user->company_id && $user->company_id !== $account->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
