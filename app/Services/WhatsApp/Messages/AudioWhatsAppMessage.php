<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for audio WhatsApp messages
 */
class AudioWhatsAppMessage extends MediaWhatsAppMessage
{
    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::AUDIO;
    }
}
