<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\RequestApprovedNotification;
use App\Notifications\RequestRejectedNotification;
use App\Notifications\RequestReturnedNotification;

class NotifyRequester implements ShouldQueue
{
    public function handle(object $event): void
    {
        $approval = $event->approval;
        $submitter = $approval->submitter;

        if (!$submitter) return;

        if ($event instanceof \App\Events\WorkflowApproved) {
            $submitter->notify(new RequestApprovedNotification($approval));
        } elseif ($event instanceof \App\Events\WorkflowRejected) {
            $submitter->notify(new RequestRejectedNotification($approval));
        } elseif ($event instanceof \App\Events\WorkflowReturned) {
            $submitter->notify(new RequestReturnedNotification($approval));
        }
    }
}
