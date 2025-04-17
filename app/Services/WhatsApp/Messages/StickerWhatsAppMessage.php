<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for sticker WhatsApp messages
 */
class StickerWhatsAppMessage extends MediaWhatsAppMessage
{
    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::STICKER;
    }
}
