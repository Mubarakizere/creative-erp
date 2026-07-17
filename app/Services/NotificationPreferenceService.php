<?php

namespace App\Services;

use App\Models\User;
use App\Models\NotificationPreference;

class NotificationPreferenceService
{
    /**
     * Get or create notification preferences for a user.
     */
    public function getPreferences(User $user): NotificationPreference
    {
        return $user->notificationPreference()->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Update notification preferences for a user.
     */
    public function updatePreferences(User $user, array $data): NotificationPreference
    {
        $preferences = $this->getPreferences($user);
        $preferences->update($data);
        return $preferences;
    }

    /**
     * Determine if a user should receive a notification via a specific channel and category.
     */
    public function shouldSendVia(User $user, string $channel, ?string $category = null): bool
    {
        $preferences = $this->getPreferences($user);

        // Global channel checks
        if ($channel === 'database' && !$preferences->database) {
            return false;
        }

        if ($channel === 'mail' && !$preferences->email) {
            return false;
        }

        // Category-specific checks
        if ($category) {
            $attributeMap = [
                'assignments' => 'assignments',
                'mentions' => 'mentions',
                'workflow' => 'workflow',
                'projects' => 'projects',
                'documents' => 'documents',
                'meetings' => 'meetings',
                'system' => 'system',
            ];

            // Normalize category to lowercase for matching
            $normalizedCategory = strtolower($category);

            foreach ($attributeMap as $key => $attribute) {
                if (str_contains($normalizedCategory, $key)) {
                    if (!$preferences->{$attribute}) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
