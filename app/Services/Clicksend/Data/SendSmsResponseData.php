<?php

namespace App\Services\Clicksend\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class SendSmsResponseData extends Data
{
    public function __construct(
        #[DataCollectionOf(SendSmsResponseMessageData::class)]
        public ?DataCollection $messages = null
    ) {}
}
