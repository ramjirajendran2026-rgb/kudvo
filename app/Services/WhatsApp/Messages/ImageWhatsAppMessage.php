<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for image WhatsApp messages
 */
class ImageWhatsAppMessage extends MediaWhatsAppMessage
{
    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::Image;
    }
}
