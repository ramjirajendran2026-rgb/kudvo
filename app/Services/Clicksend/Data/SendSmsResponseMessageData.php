<?php

namespace App\Services\Clicksend\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class SendSmsResponseMessageData extends Data
{
    public function __construct(
        public ?string $direction = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'U')]
        public ?CarbonImmutable $date = null,
        public ?string $to = null,
        public ?string $body = null,
        public ?string $from = null,
        public ?int $schedule = null,
        public ?string $message_id = null,
        public ?int $message_parts = null,
        public ?float $message_price = null,
        public ?string $from_email = null,
        public ?int $list_id = null,
        public ?string $custom_string = null,
        public ?int $contact_id = null,
        public ?int $user_id = null,
        public ?int $subaccount_id = null,
        public ?bool $is_shared_system_number = null,
        public ?string $country = null,
        public ?string $carrier = null,
        public ?string $status = null,
    ) {}
}
