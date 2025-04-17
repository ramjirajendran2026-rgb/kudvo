<?php

namespace App\Services\WhatsApp\Data;

use App\Enums\WhatsAppMessageStatus;
use Spatie\LaravelData\Data;

class SendWhatsAppMessageResponseData extends Data
{
    public function __construct(
        public ?string $messaging_product = null,
        public ?array $contacts = null,
        public ?array $messages = null,
        public ?WhatsAppMessageStatus $status = WhatsAppMessageStatus::PENDING,
        public ?string $message_id = null,
        public ?string $error_code = null,
        public ?string $error_message = null
    ) {}
}
