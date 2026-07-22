<?php

namespace App\Policies;

use App\Models\FiscalYear;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FiscalYearPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('fiscal.manage');
    }

    public function view(User $user, FiscalYear $year): bool
    {
        return $user->hasPermissionTo('fiscal.manage') && $user->company_id === $year->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('fiscal.manage');
    }

    public function update(User $user, FiscalYear $year): bool
    {
        return $user->hasPermissionTo('fiscal.manage') && $user->company_id === $year->company_id;
    }

    public function delete(User $user, FiscalYear $year): bool
    {
        return $user->hasPermissionTo('fiscal.manage') && $user->company_id === $year->company_id;
    }
}
