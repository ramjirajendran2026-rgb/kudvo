<?php

namespace App\Enums;

use Exception;

enum WhatsAppMessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case VIDEO = 'video';
    case STICKER = 'sticker';
    case LOCATION = 'location';
    case CONTACT = 'contact';
    case INTERACTIVE = 'interactive';
    case TEMPLATE = 'template';
    case REACTION = 'reaction';

    /**
     * Format the message payload based on the message type
     *
     * @throws Exception
     */
    public function formatPayload(array | string $message): array
    {
        if (is_string($message)) {
            if ($this !== self::TEXT) {
                throw new Exception('String message can only be used with TEXT type');
            }

            return [
                'preview_url' => false,
                'body' => $message,
            ];
        }

        return match ($this) {
            self::TEXT => [
                'preview_url' => $message['preview_url'] ?? false,
                'body' => $message['body'],
            ],
            self::IMAGE => $this->formatMediaPayload($message),
            self::AUDIO => $this->formatMediaPayload($message),
            self::VIDEO => $this->formatMediaPayload($message),
            self::DOCUMENT => $this->formatMediaPayload($message),
            self::STICKER => $this->formatMediaPayload($message),
            self::LOCATION => [
                'latitude' => $message['latitude'],
                'longitude' => $message['longitude'],
                'name' => $message['name'] ?? null,
                'address' => $message['address'] ?? null,
            ],
            self::CONTACT => ['contacts' => $message['contacts']],
            self::INTERACTIVE => ['interactive' => $message['interactive']],
            self::TEMPLATE => ['template' => $message['template']],
            self::REACTION => [
                'message_id' => $message['message_id'],
                'emoji' => $message['emoji'],
            ],
        };
    }

    /**
     * Format media payload for image, audio, video, document, and sticker messages
     *
     * @throws Exception
     */
    private function formatMediaPayload(array $message): array
    {
        // Handle media by ID
        if (isset($message['id'])) {
            return ['id' => $message['id']];
        }

        // Handle media by URL
        if (isset($message['link'])) {
            $payload = ['link' => $message['link']];

            // Add optional caption if provided
            if (isset($message['caption'])) {
                $payload['caption'] = $message['caption'];
            }

            // Add optional filename for documents
            if (isset($message['filename']) && $this === self::DOCUMENT) {
                $payload['filename'] = $message['filename'];
            }

            return $payload;
        }

        throw new Exception('Media must have either id or link');
    }
}
