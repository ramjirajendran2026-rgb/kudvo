<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for text WhatsApp messages
 */
class TextWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  string  $body  The text message content
     * @param  bool  $previewUrl  Whether to show URL previews in the message
     */
    public function __construct(
        protected string $body,
        protected bool $previewUrl = false
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::TEXT;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        return [
            'preview_url' => $this->previewUrl,
            'body' => $this->body,
        ];
    }
}
