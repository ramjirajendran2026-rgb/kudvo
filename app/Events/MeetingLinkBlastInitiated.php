<?php

namespace App\Events;

use App\Models\MeetingLinkBlast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingLinkBlastInitiated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public MeetingLinkBlast $blast)
    {
        $this->blast->unsetRelations();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("meetings.{$this->blast->meeting_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'meeting.participation_link_blast.initiated';
    }
}
