<?php

namespace App\Services\WhatsApp\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class WhatsAppReceiptData extends Data
{
    public function __construct(
        public ?string $messaging_product = null,
        public ?array $metadata = null,
        public ?array $contacts = null,
        public ?array $messages = null,
        public ?array $statuses = null,
        public ?string $message_id = null,
        public ?string $status = null,
        public ?string $recipient_id = null,
        public ?string $conversation = null,
        public ?string $error_code = null,
        public ?string $error_message = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'U')]
        public ?CarbonImmutable $timestamp = null,
    ) {}
}
