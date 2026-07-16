<?php

namespace App\Events;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MeetingRescheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Meeting $meeting,
        public User $user,
        public Carbon $oldStart,
        public Carbon $oldEnd,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('meetings.' . $this->meeting->company_id),
        ];
    }
}
