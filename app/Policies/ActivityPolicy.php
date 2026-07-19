<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.activities');
    }

    public function view(User $user, Activity $activity): bool
    {
        if ($user->company_id && $user->company_id !== $activity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.activities');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.activities');
    }

    public function update(User $user, Activity $activity): bool
    {
        if ($user->company_id && $user->company_id !== $activity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.activities');
    }

    public function delete(User $user, Activity $activity): bool
    {
        if ($user->company_id && $user->company_id !== $activity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.activities');
    }
}
