<?php

namespace App\Notifications\Election\Data;

use Spatie\LaravelData\Data;

class BallotLinkNotificationData extends Data
{
    public function __construct(
        public string $electionName,
        public string $ballotLink,
        public ?string $ballotLinkShort = null,
        public ?string $electorName = null,
    )
    {
    }
}
