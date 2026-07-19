<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Notification;
// Assuming NotificationService is used for sending notifications
use App\Services\NotificationService;

class CrmActivityService
{
    public function getPaginatedActivities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Activity::with(['activityable', 'assignee', 'creator']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createActivity(array $data): Activity
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['company_id'])) {
                $data['company_id'] = auth()->user()->company_id;
            }
            $data['created_by'] = auth()->id();
            
            $activity = Activity::create($data);

            if ($activity->is_reminder && $activity->reminder_at) {
                // Schedule or send notification (this depends on background job setup)
                // For now, we can log it or create a scheduled job if needed
            }

            return $activity;
        });
    }

    public function updateActivity(Activity $activity, array $data): Activity
    {
        return DB::transaction(function () use ($activity, $data) {
            $data['updated_by'] = auth()->id();
            
            $activity->update($data);

            if (isset($data['status']) && $data['status'] === 'Completed' && !$activity->completed_at) {
                $activity->update(['completed_at' => now()]);
            }

            return $activity;
        });
    }

    public function deleteActivity(Activity $activity): bool
    {
        return $activity->delete();
    }
}
