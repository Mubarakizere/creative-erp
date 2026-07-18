<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_announcements');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        // For now, any authenticated user can view an announcement if they have the link.
        // We can add stricter audience checks here if required.
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_announcements');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasPermissionTo('edit_announcements');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasPermissionTo('delete_announcements');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Announcement $announcement): bool
    {
        return $user->hasPermissionTo('publish_announcements');
    }
}
