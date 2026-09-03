<?php

namespace App\Policies;

use App\Models\User;

class FinancialReportPolicy
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

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('report.view') || $user->hasPermissionTo('financial.view') || $user->hasPermissionTo('finance.view');
    }

    public function report(User $user): bool
    {
        return $user->hasPermissionTo('report.view') || $user->hasPermissionTo('financial.report') || $user->hasPermissionTo('finance.view');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('report.view') || $user->hasPermissionTo('financial.export') || $user->hasPermissionTo('finance.view');
    }
}
