<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents\Parameters;

/**
 * Abstract base class for all template parameters
 */
abstract class TemplateParameter
{
    /**
     * Get the parameter type
     */
    abstract public function getType(): string;

    /**
     * Convert the parameter to an array for the WhatsApp API
     */
    abstract public function toArray(): array;
}
