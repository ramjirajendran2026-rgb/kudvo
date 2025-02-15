<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class TawkToConfigData extends Data
{
    public function __construct(
        public bool $enabled = false,
        public ?string $script = null,
    ) {}
}
