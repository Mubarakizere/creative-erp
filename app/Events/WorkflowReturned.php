<?php

namespace App\Events;

use App\Models\Approval;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowReturned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Approval $approval) {}
}
