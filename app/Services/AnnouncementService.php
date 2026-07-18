<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use App\Services\Realtime\RealtimeService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AnnouncementService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected RealtimeService $realtimeService
    ) {}

    /**
     * Get paginated announcements for admin management.
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Announcement::with(['creator', 'company']);

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        return $query->latest()->paginate(config('app.pagination.default', 15));
    }

    /**
     * Get active announcements visible to a specific user.
     */
    public function getVisibleForUser(User $user, int $limit = 5): Collection
    {
        return Announcement::published()
            ->where(function ($query) use ($user) {
                // Entire system
                $query->where('audience_type', 'entire_system')
                    // Specific company
                    ->orWhere(function ($q) use ($user) {
                        $q->where('audience_type', 'company')
                          ->where('audience_id', $user->company_id);
                    })
                    // Specific branch
                    ->orWhere(function ($q) use ($user) {
                        $q->where('audience_type', 'branch')
                          ->where('audience_id', $user->branch_id);
                    })
                    // Specific department
                    ->orWhere(function ($q) use ($user) {
                        $q->where('audience_type', 'department')
                          ->where('audience_id', $user->department_id);
                    })
                    // Specific roles (needs to check if user has the role, assuming spatie/laravel-permission)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('audience_type', 'role')
                          ->whereIn('audience_id', $user->roles->pluck('id'));
                    })
                    // Specific users
                    ->orWhere(function ($q) use ($user) {
                        $q->where('audience_type', 'specific_users')
                          ->whereHas('users', function ($userQuery) use ($user) {
                              $userQuery->where('users.id', $user->id);
                          });
                    });
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new announcement.
     */
    public function create(array $data, User $creator): Announcement
    {
        $data['created_by'] = $creator->id;
        $data['company_id'] = $creator->company_id; // Inherit creator's company or use selected
        
        $announcement = Announcement::create($data);

        if ($data['audience_type'] === 'specific_users' && !empty($data['user_ids'])) {
            $announcement->users()->sync($data['user_ids']);
        }

        if ($announcement->is_published) {
            $this->publish($announcement);
        }

        return $announcement;
    }

    /**
     * Update an existing announcement.
     */
    public function update(Announcement $announcement, array $data, User $updater): Announcement
    {
        $data['updated_by'] = $updater->id;
        
        $wasPublished = $announcement->is_published;
        
        $announcement->update($data);

        if ($data['audience_type'] === 'specific_users' && !empty($data['user_ids'])) {
            $announcement->users()->sync($data['user_ids']);
        } elseif ($data['audience_type'] !== 'specific_users') {
            $announcement->users()->detach();
        }

        // If it just became published
        if (!$wasPublished && $announcement->is_published) {
            $this->publish($announcement);
        }

        return $announcement;
    }

    /**
     * Publish an announcement.
     */
    public function publish(Announcement $announcement): void
    {
        if (!$announcement->published_at) {
            $announcement->update(['published_at' => now(), 'is_published' => true]);
        } else {
            $announcement->update(['is_published' => true]);
        }

        // Identify target audience
        $notifiables = $this->getTargetUsers($announcement);

        if ($notifiables->isNotEmpty()) {
            $this->notificationService->send($notifiables, new AnnouncementNotification($announcement));
        }

        // Realtime broadcast (if enabled)
        if ($this->realtimeService->isEnabled() && config('realtime.features.announcements')) {
            // $this->realtimeService->broadcast()->dispatch(new \App\Events\AnnouncementPublished($announcement));
            // simplified for brevity
        }
    }

    /**
     * Unpublish an announcement.
     */
    public function unpublish(Announcement $announcement): void
    {
        $announcement->update(['is_published' => false]);
    }

    /**
     * Delete an announcement.
     */
    public function delete(Announcement $announcement): bool
    {
        return $announcement->delete();
    }

    /**
     * Get users targeted by this announcement.
     */
    protected function getTargetUsers(Announcement $announcement): Collection
    {
        $query = User::active();

        switch ($announcement->audience_type) {
            case 'company':
                $query->where('company_id', $announcement->audience_id);
                break;
            case 'branch':
                $query->where('branch_id', $announcement->audience_id);
                break;
            case 'department':
                $query->where('department_id', $announcement->audience_id);
                break;
            case 'role':
                $query->role($announcement->audience_id);
                break;
            case 'specific_users':
                return $announcement->users()->active()->get();
            case 'entire_system':
            default:
                // No additional filtering
                break;
        }

        return $query->get();
    }
}
