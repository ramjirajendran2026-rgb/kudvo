<?php

namespace App\Services\TwentyFourSevenSms;

use Illuminate\Foundation\Events\Dispatchable;

class SmsMessageSent
{
    use Dispatchable;

    public function __construct(
        public string $response
    ) {
    }
}
