<?php

namespace App\Policies;

use App\Models\AccountingPeriod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountingPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('period.manage');
    }

    public function view(User $user, AccountingPeriod $period): bool
    {
        return $user->hasPermissionTo('period.manage') && $user->company_id === $period->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('period.manage');
    }

    public function update(User $user, AccountingPeriod $period): bool
    {
        return $user->hasPermissionTo('period.manage') && $user->company_id === $period->company_id;
    }

    public function delete(User $user, AccountingPeriod $period): bool
    {
        return $user->hasPermissionTo('period.manage') && $user->company_id === $period->company_id;
    }
}
