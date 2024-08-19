<?php

namespace App\Services\Clicksend\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class SmsReceiptData extends Data
{
    public function __construct(
        public ?int $error_code = null,
        public ?string $error_text = null,
        public ?string $custom_string = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'U')]
        public ?CarbonImmutable $timestamp_send = null,
        public ?string $message_id = null,
        public ?int $user_id = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'U')]
        public ?CarbonImmutable $timestamp = null,
        public ?string $messageid = null,
        public ?string $customstring = null,
        public ?string $status_text = null,
        public ?int $subaccount_id = null,
        public ?string $message_type = null,
        public ?string $status = null,
        public ?int $status_code = null,
    ) {}
}
