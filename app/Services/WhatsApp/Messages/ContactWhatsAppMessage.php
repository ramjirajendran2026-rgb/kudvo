<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for contact WhatsApp messages
 */
class ContactWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  array  $contacts  Array of contact data
     */
    public function __construct(
        protected array $contacts
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::CONTACT;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        return [
            'contacts' => $this->contacts,
        ];
    }
}
