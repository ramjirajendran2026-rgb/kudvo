<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;
use Exception;

/**
 * Abstract base class for media WhatsApp messages (image, audio, video, document, sticker)
 */
abstract class MediaWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     * @param  string|null  $caption  Optional caption for the media
     * @param  string|null  $filename  Optional filename (for documents only)
     *
     * @throws Exception If neither id nor link is provided
     */
    public function __construct(
        protected ?string $id = null,
        protected ?string $link = null,
        protected ?string $caption = null,
        protected ?string $filename = null
    ) {
        if (is_null($this->id) && is_null($this->link)) {
            throw new Exception('Media must have either id or link');
        }
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        // Handle media by ID
        if (! is_null($this->id)) {
            return ['id' => $this->id];
        }

        // Handle media by URL
        $payload = ['link' => $this->link];

        // Add optional caption if provided
        if (! is_null($this->caption)) {
            $payload['caption'] = $this->caption;
        }

        // Add optional filename for documents
        if (! is_null($this->filename) && $this->getType() === WhatsAppMessageType::DOCUMENT) {
            $payload['filename'] = $this->filename;
        }

        return $payload;
    }
}
