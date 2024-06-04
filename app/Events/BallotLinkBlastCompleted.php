<?php

namespace App\Events;

use App\Models\BallotLinkBlast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BallotLinkBlastCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BallotLinkBlast $blast)
    {
        $this->blast->unsetRelations();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("elections.{$this->blast->election_id}"),
        ];
    }
}
