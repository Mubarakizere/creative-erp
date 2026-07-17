<?php

namespace App\Listeners;

use App\Events\ApprovalAssigned;
use App\Notifications\ApprovalAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class NotifyNextApprover implements ShouldQueue
{
    public function handle(ApprovalAssigned $event): void
    {
        $step = $event->step;
        $approval = $event->approval;

        if ($step->approver_user_id) {
            $user = User::find($step->approver_user_id);
            if ($user) {
                $user->notify(new ApprovalAssignedNotification($approval));
            }
        } elseif ($step->approver_role_id) {
            $users = User::role($step->role->name)->get();
            foreach ($users as $user) {
                $user->notify(new ApprovalAssignedNotification($approval));
            }
        }
    }
}
