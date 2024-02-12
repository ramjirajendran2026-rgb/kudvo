<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ElectorBallotLinkNotificationData extends Data
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
