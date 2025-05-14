<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class FacebookConfigData extends Data
{
    public function __construct(
        public bool $enabled = false,
        public ?string $client_id = null,
        public ?string $client_secret = null,
    ) {}
}
