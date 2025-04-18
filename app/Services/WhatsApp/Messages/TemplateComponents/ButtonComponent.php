<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents;

/**
 * Class for button template components
 */
class ButtonComponent extends TemplateComponent
{
    /**
     * @param  string  $subType  The button sub-type (e.g., 'quick_reply', 'url')
     * @param  array  $parameters  The button parameters
     */
    public function __construct(
        protected string $subType,
        protected array $parameters = [],
        protected int $index = 0,
    ) {}

    /**
     * Get the component type
     */
    public function getType(): string
    {
        return 'button';
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Get the component parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get the button sub-type
     */
    public function getSubType(): string
    {
        return $this->subType;
    }

    /**
     * Set a parameter value
     */
    public function setParameter(string $key, mixed $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * Convert the component to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'sub_type' => $this->getSubType(),
            'index' => $this->getIndex(),
            'parameters' => $this->getParameters(),
        ];
    }

    /**
     * Create a quick reply button
     */
    public static function quickReply(string $payload, ?string $text = null): self
    {
        $parameters = ['payload' => $payload];

        if ($text !== null) {
            $parameters['text'] = $text;
        }

        return new self('quick_reply', $parameters);
    }

    /**
     * Create a URL button
     */
    public static function url(string $text): self
    {
        return new self('url', [TemplateComponentFactory::textParameter($text)->toArray()]);
    }

    /**
     * Create a URL button
     */
    public static function flow(): self
    {
        return new self('flow');
    }
}
