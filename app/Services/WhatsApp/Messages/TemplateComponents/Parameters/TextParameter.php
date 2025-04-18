<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents\Parameters;

/**
 * Class for text template parameters
 */
class TextParameter extends TemplateParameter
{
    /**
     * @param  string  $text  The text value
     * @param  string|null  $name  Optional name value
     */
    public function __construct(
        protected string $text,
        protected ?string $name,
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
        $payload = [
            'type' => $this->getType(),
            'text' => $this->text,
        ];

        if (filled($this->name)) {
            $payload['parameter_name'] = $this->name;
        }

        return $payload;
    }
}
