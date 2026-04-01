<?php
namespace App\Data;

use Spatie\LaravelData\Data;

class TwentyFourSevenSmsConfigData extends Data
{
    public function __construct(
        public ?string $api_key = null,
        public ?string $sender_id = null,
    ) {}

    /**
     * Use a custom name to avoid the "compatible with" error.
     */
    public static function fromValue(mixed $payload): self
    {
        if (empty($payload) || is_null($payload)) {
            return new self(null, null);
        }

        // Handle if payload is already an array from the DB JSON
        return new self(
            $payload['api_key'] ?? null,
            $payload['sender_id'] ?? null
        );
    }
}