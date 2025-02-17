<?php

namespace App\Events\Meeting;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantImportCompleted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly int $meetingId,
        public readonly int $importId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(name: 'meetings.' . $this->meetingId),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'participant-import-completed';
    }
}
