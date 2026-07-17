<?php

namespace App\Observers;

use App\Models\Notification;

class NotificationObserver
{
    public function creating(Notification $notification): void
    {
        $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
        
        if (is_array($data)) {
            $notification->category = $data['category'] ?? null;
            $notification->priority = $data['priority'] ?? 'Normal';
            $notification->icon = $data['icon'] ?? null;
            $notification->color = $data['color'] ?? null;
            $notification->action_url = $data['action_url'] ?? null;
            $notification->action_text = $data['action_text'] ?? null;
            $notification->company_id = $data['company_id'] ?? null;
            $notification->branch_id = $data['branch_id'] ?? null;
            $notification->created_by = $data['created_by'] ?? null;
        }
    }
}
