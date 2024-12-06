<?php

namespace App\Actions;

use App\Models\ShortLink;
use Hashids\Hashids;

class GenerateShortLinkKey
{
    public function __construct(
        protected Hashids $hashids,
    ) {}

    public function execute(): string
    {
        $lastId = ShortLink::max(app(ShortLink::class)->getKeyName()) ?? 0;
        $lastId++;

        return $this->hashids->encode($lastId);
    }
}
