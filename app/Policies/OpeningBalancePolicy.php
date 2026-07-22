<?php

namespace App\Policies;

use App\Models\OpeningBalance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OpeningBalancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('account.view');
    }

    public function view(User $user, OpeningBalance $balance): bool
    {
        return $user->hasPermissionTo('account.view') && $user->company_id === $balance->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('account.create');
    }

    public function update(User $user, OpeningBalance $balance): bool
    {
        return $user->hasPermissionTo('account.update') && $user->company_id === $balance->company_id;
    }

    public function delete(User $user, OpeningBalance $balance): bool
    {
        return $user->hasPermissionTo('account.delete') && $user->company_id === $balance->company_id;
    }
}
