<?php

namespace App\Policies;

use App\Models\User;

class ExecutiveDashboardPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user): bool
    {
        return $user->can('executive.dashboard');
    }
}
