<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', $model->getAttributes());
        });

        static::updated(function ($model) {
            // Only log if something actually changed
            if ($model->wasChanged()) {
                $changes = $model->getChanges();
                // We don't need to log updated_at
                unset($changes['updated_at']);
                
                if (!empty($changes)) {
                    $model->logActivity('updated', $changes);
                }
            }
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getAttributes());
        });
    }

    public function logActivity(string $action, array $properties = [])
    {
        // Don't log if running from console without an authenticated user, unless we specifically want to
        $userId = auth()->id() ?? 1; // Fallback to 1 for system/seeders
        $companyId = session('company_id') ?? ($this->company_id ?? 1);

        ActivityLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => $action,
            'subject_type' => get_class($this),
            'subject_id' => $this->id ?? 0,
            'properties' => $properties,
        ]);
    }
}
