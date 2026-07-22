<?php

namespace App\Policies;

use App\Models\User;

class AnalyticsPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user): bool
    {
        return $user->can('analytics.view');
    }
}
