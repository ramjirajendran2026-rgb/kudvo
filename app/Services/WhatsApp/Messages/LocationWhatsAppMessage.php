<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;

/**
 * Class for location WhatsApp messages
 */
class LocationWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  float  $latitude  The latitude coordinate
     * @param  float  $longitude  The longitude coordinate
     * @param  string|null  $name  Optional location name
     * @param  string|null  $address  Optional location address
     */
    public function __construct(
        protected float $latitude,
        protected float $longitude,
        protected ?string $name = null,
        protected ?string $address = null
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::Location;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        $payload = [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if (! is_null($this->name)) {
            $payload['name'] = $this->name;
        }

        if (! is_null($this->address)) {
            $payload['address'] = $this->address;
        }

        return $payload;
    }
}
