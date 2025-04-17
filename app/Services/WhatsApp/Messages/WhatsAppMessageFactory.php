<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponent;
use Exception;

/**
 * Factory class for creating WhatsApp messages
 */
class WhatsAppMessageFactory
{
    /**
     * Create a text message
     *
     * @param  string  $body  The text message content
     * @param  bool  $previewUrl  Whether to show URL previews in the message
     */
    public static function text(string $body, bool $previewUrl = false): TextWhatsAppMessage
    {
        return new TextWhatsAppMessage($body, $previewUrl);
    }

    /**
     * Create a template message
     *
     * @param  string  $name  The template name
     * @param  string  $language  The language code (e.g., 'en_US')
     * @param  array<TemplateComponent>  $components  The template components
     */
    public static function template(string $name, string $language, array $components = []): TemplateWhatsAppMessage
    {
        return TemplateWhatsAppMessage::create($name, $language, $components);
    }

    /**
     * Create an image message
     *
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     * @param  string|null  $caption  Optional caption for the image
     *
     * @throws Exception If neither id nor link is provided
     */
    public static function image(?string $id = null, ?string $link = null, ?string $caption = null): ImageWhatsAppMessage
    {
        return new ImageWhatsAppMessage($id, $link, $caption);
    }

    /**
     * Create an audio message
     *
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     *
     * @throws Exception If neither id nor link is provided
     */
    public static function audio(?string $id = null, ?string $link = null): AudioWhatsAppMessage
    {
        return new AudioWhatsAppMessage($id, $link);
    }

    /**
     * Create a video message
     *
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     * @param  string|null  $caption  Optional caption for the video
     *
     * @throws Exception If neither id nor link is provided
     */
    public static function video(?string $id = null, ?string $link = null, ?string $caption = null): VideoWhatsAppMessage
    {
        return new VideoWhatsAppMessage($id, $link, $caption);
    }

    /**
     * Create a document message
     *
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     * @param  string|null  $caption  Optional caption for the document
     * @param  string|null  $filename  Optional filename for the document
     *
     * @throws Exception If neither id nor link is provided
     */
    public static function document(?string $id = null, ?string $link = null, ?string $caption = null, ?string $filename = null): DocumentWhatsAppMessage
    {
        return new DocumentWhatsAppMessage($id, $link, $caption, $filename);
    }

    /**
     * Create a sticker message
     *
     * @param  string|null  $id  The media ID (if using uploaded media)
     * @param  string|null  $link  The media URL (if using external media)
     *
     * @throws Exception If neither id nor link is provided
     */
    public static function sticker(?string $id = null, ?string $link = null): StickerWhatsAppMessage
    {
        return new StickerWhatsAppMessage($id, $link);
    }

    /**
     * Create a location message
     *
     * @param  float  $latitude  The latitude coordinate
     * @param  float  $longitude  The longitude coordinate
     * @param  string|null  $name  Optional location name
     * @param  string|null  $address  Optional location address
     */
    public static function location(float $latitude, float $longitude, ?string $name = null, ?string $address = null): LocationWhatsAppMessage
    {
        return new LocationWhatsAppMessage($latitude, $longitude, $name, $address);
    }

    /**
     * Create a contact message
     *
     * @param  array  $contacts  Array of contact data
     */
    public static function contact(array $contacts): ContactWhatsAppMessage
    {
        return new ContactWhatsAppMessage($contacts);
    }

    /**
     * Create an interactive message
     *
     * @param  array  $interactive  Interactive message data
     */
    public static function interactive(array $interactive): InteractiveWhatsAppMessage
    {
        return new InteractiveWhatsAppMessage($interactive);
    }

    /**
     * Create a reaction message
     *
     * @param  string  $messageId  The ID of the message being reacted to
     * @param  string  $emoji  The emoji reaction
     */
    public static function reaction(string $messageId, string $emoji): ReactionWhatsAppMessage
    {
        return new ReactionWhatsAppMessage($messageId, $emoji);
    }

    /**
     * Create a message from an array
     *
     * @param  array  $data  The message data
     *
     * @throws Exception If the message type is not supported
     */
    public static function fromArray(array $data): WhatsAppMessage
    {
        if (! isset($data['type'])) {
            throw new Exception('Message type is required');
        }

        $type = $data['type'];

        return match ($type) {
            WhatsAppMessageType::Text->value => self::text(
                $data['body'] ?? '',
                $data['preview_url'] ?? false
            ),
            WhatsAppMessageType::Template->value => self::template(
                $data['template']['name'] ?? '',
                $data['template']['language']['code'] ?? 'en_US',
                [] // Components need to be created from the raw array data
            ),
            WhatsAppMessageType::Image->value => self::image(
                $data['id'] ?? null,
                $data['link'] ?? null,
                $data['caption'] ?? null
            ),
            WhatsAppMessageType::Audio->value => self::audio(
                $data['id'] ?? null,
                $data['link'] ?? null
            ),
            WhatsAppMessageType::Video->value => self::video(
                $data['id'] ?? null,
                $data['link'] ?? null,
                $data['caption'] ?? null
            ),
            WhatsAppMessageType::Document->value => self::document(
                $data['id'] ?? null,
                $data['link'] ?? null,
                $data['caption'] ?? null,
                $data['filename'] ?? null
            ),
            WhatsAppMessageType::Sticker->value => self::sticker(
                $data['id'] ?? null,
                $data['link'] ?? null
            ),
            WhatsAppMessageType::Location->value => self::location(
                $data['latitude'] ?? 0,
                $data['longitude'] ?? 0,
                $data['name'] ?? null,
                $data['address'] ?? null
            ),
            WhatsAppMessageType::Contact->value => self::contact(
                $data['contacts'] ?? []
            ),
            WhatsAppMessageType::Interactive->value => self::interactive(
                $data['interactive'] ?? []
            ),
            WhatsAppMessageType::Reaction->value => self::reaction(
                $data['message_id'] ?? '',
                $data['emoji'] ?? ''
            ),
            default => throw new Exception("Unsupported message type: {$type}")
        };
    }
}
