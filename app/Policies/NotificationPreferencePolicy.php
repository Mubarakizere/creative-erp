<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NotificationPreference;

class NotificationPreferencePolicy
{
    public function view(User $user, NotificationPreference $preference): bool
    {
        return $user->id === $preference->user_id;
    }

    public function update(User $user, NotificationPreference $preference): bool
    {
        return $user->id === $preference->user_id;
    }
}
