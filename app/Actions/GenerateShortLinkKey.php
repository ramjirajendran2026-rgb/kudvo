<?php

namespace App\Actions;

use App\Models\ShortLink;
use Hashids\Hashids;

class GenerateShortLinkKey
{
    public function __construct(
        protected Hashids $hashids,
        protected ShortLink $shortLink,
    ) {}

    public function execute(): string
    {
        return $this->hashids->encode($this->shortLink->getNextPossibleKey());
    }
}
