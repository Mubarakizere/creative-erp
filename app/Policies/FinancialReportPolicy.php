<?php

namespace App\Policies;

use App\Models\User;

class FinancialReportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user): bool
    {
        return $user->can('financial.view');
    }

    public function report(User $user): bool
    {
        return $user->can('financial.report');
    }

    public function export(User $user): bool
    {
        return $user->can('financial.export');
    }
}
