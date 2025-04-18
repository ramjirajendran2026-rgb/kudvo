<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for interactive WhatsApp messages
 */
class InteractiveWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  array  $interactive  Interactive message data
     */
    public function __construct(
        protected array $interactive
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::Interactive;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        return [
            'interactive' => $this->interactive,
        ];
    }
}
