<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimeEntryPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('time.view');
    }

    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return $user->can('time.view') && 
               ($user->id === $timeEntry->user_id || $user->can('time.approve'));
    }

    public function create(User $user): bool
    {
        return $user->can('time.create');
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->can('time.update') && 
               ($user->id === $timeEntry->user_id || $user->can('time.approve'));
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->can('time.delete') && 
               ($user->id === $timeEntry->user_id || $user->can('time.approve'));
    }

    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        return $user->can('time.restore');
    }

    public function export(User $user): bool
    {
        return $user->can('time.export');
    }
}
