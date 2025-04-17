<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents\Parameters;

/**
 * Class for date_time template parameters
 */
class DateTimeParameter extends TemplateParameter
{
    /**
     * @param  string  $fallbackValue  The fallback value
     * @param  \DateTime|null  $dateTime  Optional DateTime object
     */
    public function __construct(
        protected string $fallbackValue,
        protected ?\DateTime $dateTime = null
    ) {}

    /**
     * Get the parameter type
     */
    public function getType(): string
    {
        return 'date_time';
    }

    /**
     * Convert the parameter to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        $dateTime = [
            'fallback_value' => $this->fallbackValue,
        ];

        if ($this->dateTime !== null) {
            $dateTime['timestamp'] = $this->dateTime->getTimestamp();
        }

        return [
            'type' => $this->getType(),
            'date_time' => $dateTime,
        ];
    }
}
