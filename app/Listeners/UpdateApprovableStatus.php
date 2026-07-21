<?php

namespace App\Listeners;

use App\Events\WorkflowApproved;
use App\Events\WorkflowRejected;
use App\Models\Invoice;
use App\Models\Refund;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateApprovableStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $approval = $event->approval;
        $approvable = $approval->approvable;

        if (!$approvable) {
            return;
        }

        $isApproved = $event instanceof WorkflowApproved;

        if ($approvable instanceof Invoice) {
            $approvable->update([
                'status' => $isApproved ? 'Issued' : 'Draft' // Back to draft if rejected? Or Rejected? Let's use Draft so they can fix it. Or maybe 'Rejected' if that exists. But we only have Draft/Issued. We'll use Draft.
            ]);
        } elseif ($approvable instanceof Refund) {
            $approvable->update([
                'status' => $isApproved ? 'Approved' : 'Rejected'
            ]);
        }
    }
}

