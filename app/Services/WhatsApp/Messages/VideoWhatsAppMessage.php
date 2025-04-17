<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for video WhatsApp messages
 */
class VideoWhatsAppMessage extends MediaWhatsAppMessage
{
    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::VIDEO;
    }
}
