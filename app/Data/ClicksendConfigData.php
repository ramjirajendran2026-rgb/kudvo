<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ClicksendConfigData extends Data
{
    public function __construct(
        public ?string $username,
        public ?string $api_key ,
    ) {}
}
