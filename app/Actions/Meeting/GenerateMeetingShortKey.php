<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;
use Hashids\Hashids;

class GenerateMeetingShortKey
{
    public function __construct(
        protected Hashids $hashids,
        protected Meeting $meeting,
    ) {}

    public function execute(): string
    {
        return $this->hashids->encode($this->meeting->getNextPossibleKey());
    }
}
