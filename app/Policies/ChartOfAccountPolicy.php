<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChartOfAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('account.view');
    }

    public function view(User $user, ChartOfAccount $account): bool
    {
        return $user->hasPermissionTo('account.view') && $user->company_id === $account->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('account.create');
    }

    public function update(User $user, ChartOfAccount $account): bool
    {
        return $user->hasPermissionTo('account.update') && $user->company_id === $account->company_id;
    }

    public function delete(User $user, ChartOfAccount $account): bool
    {
        return $user->hasPermissionTo('account.delete') && $user->company_id === $account->company_id && !$account->is_system;
    }
}
