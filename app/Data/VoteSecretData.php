<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class VoteSecretData extends Data
{
    public function __construct(
        public string $key,
        public int $value,
    )
    {
    }
}
