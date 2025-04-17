<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Abstract base class for all WhatsApp message types
 */
abstract class WhatsAppMessage
{
    /**
     * Get the message type
     */
    abstract public function getType(): WhatsAppMessageType;

    /**
     * Format the message payload for the WhatsApp API
     */
    abstract public function formatPayload(): array;

    /**
     * Convert the message to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType()->value,
            $this->getType()->value => $this->formatPayload(),
        ];
    }
}
