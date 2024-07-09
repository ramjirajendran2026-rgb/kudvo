<?php

namespace App\Events\Election\Booth;

use App\Models\ElectionBoothToken;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Activated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly string $boothId) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(name: 'elections.'.ElectionBoothToken::find($this->boothId)?->election_id),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'booth-activated';
    }
}
