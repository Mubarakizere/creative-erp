<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public function __construct(
        protected NotificationPreferenceService $preferenceService
    ) {}

    /**
     * Dispatch a notification to notifiable entities.
     */
    public function send($notifiables, $notification): void
    {
        NotificationFacade::send($notifiables, $notification);
        $this->clearCacheFor($notifiables);
    }

    /**
     * Get paginated notifications for a user with filters.
     */
    public function getNotificationsForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->notifications();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('data', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('category', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'unread') {
                $query->whereNull('read_at');
            } elseif ($filters['status'] === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        return $query->paginate(config('app.pagination.default', 15));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->clearCacheFor($user);
            return true;
        }
        return false;
    }

    /**
     * Mark a specific notification as unread.
     */
    public function markAsUnread(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->read_at = null;
            $notification->save();
            $this->clearCacheFor($user);
            return true;
        }
        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
        $this->clearCacheFor($user);
    }

    /**
     * Delete a specific notification.
     */
    public function delete(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
            $this->clearCacheFor($user);
            return true;
        }
        return false;
    }

    /**
     * Perform bulk actions on notifications.
     */
    public function bulkAction(User $user, array $notificationIds, string $action): void
    {
        $query = $user->notifications()->whereIn('id', $notificationIds);
        
        switch ($action) {
            case 'read':
                $query->update(['read_at' => now()]);
                break;
            case 'unread':
                $query->update(['read_at' => null]);
                break;
            case 'delete':
                $query->delete();
                break;
        }
        $this->clearCacheFor($user);
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadCount(User $user): int
    {
        return Cache::remember("notifications.unread_count.{$user->id}", 300, function () use ($user) {
            return $user->unreadNotifications()->count();
        });
    }

    /**
     * Get recent notifications.
     */
    public function getRecentNotifications(User $user, int $limit = 5): Collection
    {
        return Cache::remember("notifications.recent.{$user->id}", 300, function () use ($user, $limit) {
            return $user->notifications()->limit($limit)->get();
        });
    }

    /**
     * Clear cache for the given notifiable entity or collection of entities.
     */
    public function clearCacheFor($notifiables): void
    {
        if ($notifiables instanceof User) {
            Cache::forget("notifications.unread_count.{$notifiables->id}");
            Cache::forget("notifications.recent.{$notifiables->id}");
        } elseif (is_iterable($notifiables)) {
            foreach ($notifiables as $user) {
                if ($user instanceof User) {
                    Cache::forget("notifications.unread_count.{$user->id}");
                    Cache::forget("notifications.recent.{$user->id}");
                }
            }
        }
    }
}
