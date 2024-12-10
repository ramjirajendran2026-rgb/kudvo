<?php

namespace App\Actions;

use App\Models\Participant;
use Hashids\Hashids;

class GenerateParticipantShortKey
{
    public function __construct(
        protected Hashids $hashids,
        protected Participant $participant,
    ) {}

    public function execute(): string
    {
        return $this->hashids->encode($this->participant->getNextPossibleKey());
    }
}
