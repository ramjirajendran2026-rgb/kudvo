<?php

namespace App\Events\Election\Booth;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrintBallot implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly string $boothId) {}

    public function broadcastOn(): array
    {
        return [
            new Channel(name: 'election-booth.' . $this->boothId),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'booth-print-ballot';
    }
}
