<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for document WhatsApp messages
 */
class DocumentWhatsAppMessage extends MediaWhatsAppMessage
{
    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::DOCUMENT;
    }
}
