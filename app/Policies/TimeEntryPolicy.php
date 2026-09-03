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
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TimeEntry $timeEntry): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $timeEntry->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        $project = $timeEntry->project ?? $timeEntry->task?->project;
        if ($project && $project->hasPermissionForUser($user, 'time.view')) {
            return true;
        }

        return $user->id === $timeEntry->user_id || $user->hasPermissionTo('time.view') || $user->hasPermissionTo('time.approve');
    }

    public function create(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        $project = $arg1 instanceof \App\Models\Project ? $arg1 : ($arg2 instanceof \App\Models\Project ? $arg2 : null);
        if ($project) {
            return $project->hasPermissionForUser($user, 'time.create');
        }

        return $user->hasPermissionTo('time.create');
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $timeEntry->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        $project = $timeEntry->project ?? $timeEntry->task?->project;
        if ($project && $project->hasPermissionForUser($user, 'time.update')) {
            return true;
        }

        return $user->id === $timeEntry->user_id || $user->hasPermissionTo('time.update') || $user->hasPermissionTo('time.approve');
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $timeEntry->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        $project = $timeEntry->project ?? $timeEntry->task?->project;
        if ($project && $project->hasPermissionForUser($user, 'time.delete')) {
            return true;
        }

        return $user->id === $timeEntry->user_id || $user->hasPermissionTo('time.delete') || $user->hasPermissionTo('time.approve');
    }

    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $timeEntry->company_id && $user->company_id !== $timeEntry->company_id) {
            return false;
        }

        return $user->hasPermissionTo('time.restore');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('time.export') || $user->hasPermissionTo('time.view') || $user->hasPermissionTo('report.view');
    }

    public function viewReports(User $user): bool
    {
        return $user->hasPermissionTo('time.view') || $user->hasPermissionTo('report.view') || $user->hasPermissionTo('time.export');
    }
}
