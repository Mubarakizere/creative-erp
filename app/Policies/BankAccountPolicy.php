<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;

class BankAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('bank_account.view');
    }

    public function view(User $user, BankAccount $bankAccount): bool
    {
        if ($user->company_id && $user->company_id !== $bankAccount->company_id) {
            return false;
        }

        return $user->hasPermissionTo('bank_account.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('bank_account.create');
    }

    public function update(User $user, BankAccount $bankAccount): bool
    {
        if ($user->company_id && $user->company_id !== $bankAccount->company_id) {
            return false;
        }

        return $user->hasPermissionTo('bank_account.update');
    }

    public function delete(User $user, BankAccount $bankAccount): bool
    {
        if ($user->company_id && $user->company_id !== $bankAccount->company_id) {
            return false;
        }

        return $user->hasPermissionTo('bank_account.delete');
    }
}
