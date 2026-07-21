<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected function logActivity(string $action, $subject, array $properties = []): void
    {
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? ($subject->company_id ?? 1),
                'action' => $action,
                'subject_type' => get_class($subject),
                'subject_id' => $subject->id,
                'properties' => $properties,
            ]);
        }
    }
}
