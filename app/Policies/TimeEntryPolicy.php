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
        if (!$user->can('time.view')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        return $user->id === $timeEntry->user_id || $user->can('time.approve');
    }

    public function create(User $user): bool
    {
        return $user->can('time.create');
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        if (!$user->can('time.update')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        return $user->id === $timeEntry->user_id || $user->can('time.approve');
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        if (!$user->can('time.delete')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        return $user->id === $timeEntry->user_id || $user->can('time.approve');
    }

    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        if (!$user->can('time.restore')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        return true;
    }

    public function export(User $user): bool
    {
        return $user->can('time.export');
    }
}
