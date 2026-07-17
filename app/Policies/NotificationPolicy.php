<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('notification.view');
    }

    public function view(User $user, Notification $notification): bool
    {
        if (!$user->hasPermissionTo('notification.view')) {
            return false;
        }
        return $user->id === $notification->notifiable_id;
    }

    public function updateAny(User $user): bool
    {
        return $user->hasPermissionTo('notification.manage');
    }

    public function update(User $user, Notification $notification): bool
    {
        if (!$user->hasPermissionTo('notification.manage')) {
            return false;
        }
        return $user->id === $notification->notifiable_id;
    }

    public function delete(User $user, Notification $notification): bool
    {
        if (!$user->hasPermissionTo('notification.delete')) {
            return false;
        }
        return $user->id === $notification->notifiable_id;
    }
}
