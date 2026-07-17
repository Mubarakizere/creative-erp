<?php

namespace App\Events;

use App\Models\Approval;
use App\Models\WorkflowStep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Approval $approval, public WorkflowStep $step) {}
}
