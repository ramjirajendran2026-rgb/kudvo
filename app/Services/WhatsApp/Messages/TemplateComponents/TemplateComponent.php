<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents;

/**
 * Abstract base class for all template components
 */
abstract class TemplateComponent
{
    /**
     * Get the component type
     */
    abstract public function getType(): string;

    /**
     * Get the component parameters
     */
    abstract public function getParameters(): array;

    /**
     * Convert the component to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'parameters' => $this->getParameters(),
        ];
    }
}
