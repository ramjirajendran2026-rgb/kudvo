<?php

namespace App\Events;

use App\Models\ElectionBoothToken;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElectorRevokedFromBoothEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly string $boothId) {}

    public function broadcastOn(): array
    {
        return [
            new Channel(name: 'election-booth.' . $this->boothId),
            new PrivateChannel(name: 'elections.' . ElectionBoothToken::find($this->boothId)?->election_id),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'booth-elector-revoked';
    }
}
