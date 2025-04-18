<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for reaction WhatsApp messages
 */
class ReactionWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  string  $messageId  The ID of the message being reacted to
     * @param  string  $emoji  The emoji reaction
     */
    public function __construct(
        protected string $messageId,
        protected string $emoji
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::Reaction;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        return [
            'message_id' => $this->messageId,
            'emoji' => $this->emoji,
        ];
    }
}
