<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        if ($meeting->project) {
            return $meeting->project->hasPermissionForUser($user, 'meeting.view');
        }
        return $user->hasPermissionTo('meeting.view') || $meeting->isOrganizer($user) || $meeting->attendees()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, mixed $arg1 = null, mixed $arg2 = null): bool
    {
        $project = $arg1 instanceof \App\Models\Project ? $arg1 : ($arg2 instanceof \App\Models\Project ? $arg2 : null);
        if ($project) {
            return $project->hasPermissionForUser($user, 'meeting.create');
        }
        return $user->hasPermissionTo('meeting.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        if ($meeting->project) {
            return $meeting->project->hasPermissionForUser($user, 'meeting.update') || $meeting->isOrganizer($user);
        }
        return $user->hasPermissionTo('meeting.update') || $meeting->isOrganizer($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        if ($meeting->project) {
            return $meeting->project->hasPermissionForUser($user, 'meeting.delete') || $meeting->isOrganizer($user);
        }
        return $user->hasPermissionTo('meeting.delete') || $meeting->isOrganizer($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        return $user->hasPermissionTo('meeting.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Meeting $meeting): bool
    {
        return false; // Soft deletes only
    }

    /**
     * Determine whether the user can invite attendees.
     */
    public function invite(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        return $user->hasPermissionTo('meeting.invite') || $meeting->isOrganizer($user);
    }

    /**
     * Determine whether the user can cancel the meeting.
     */
    public function cancel(User $user, Meeting $meeting): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }
        if ($user->company_id && $meeting->company_id && $user->company_id !== $meeting->company_id) {
            return false;
        }
        if ($meeting->project) {
            return $meeting->project->hasPermissionForUser($user, 'meeting.cancel') || $meeting->project->hasPermissionForUser($user, 'meeting.update') || $meeting->isOrganizer($user);
        }
        return $user->hasPermissionTo('meeting.cancel') || $user->hasPermissionTo('meeting.update') || $meeting->isOrganizer($user);
    }

    /**
     * Determine whether the user can respond to an invitation.
     */
    public function respond(User $user, Meeting $meeting): bool
    {
        return $meeting->attendees()->where('users.id', $user->id)->exists();
    }
}
