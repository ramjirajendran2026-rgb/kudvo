<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class TwentyFourSevenSmsConfigData extends Data
{
    public function __construct(
        public ?string $api_key = null,
        public ?string $sender_id = null,
    )
    {
    }
}
