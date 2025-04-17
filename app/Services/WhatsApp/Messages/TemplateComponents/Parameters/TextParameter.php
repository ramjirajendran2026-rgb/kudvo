<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents\Parameters;

/**
 * Class for text template parameters
 */
class TextParameter extends TemplateParameter
{
    /**
     * @param  string  $text  The text value
     */
    public function __construct(
        protected string $text
    ) {}

    /**
     * Get the parameter type
     */
    public function getType(): string
    {
        return 'text';
    }

    /**
     * Convert the parameter to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'text' => $this->text,
        ];
    }
}
