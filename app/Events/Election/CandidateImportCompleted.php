<?php

namespace App\Events\Election;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidateImportCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $electionId,
        public readonly int $importId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(name: 'elections.'.$this->electionId),
        ];
    }

    public function broadcastAs(): string
    {
        return static::getBroadcastName();
    }

    public static function getBroadcastName(): string
    {
        return 'candidate-import-completed';
    }
}
