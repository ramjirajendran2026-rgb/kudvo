<?php

namespace App\Services\WhatsApp\Messages;

use App\Enums\WhatsAppMessageType;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponent;

/**
 * Class for template WhatsApp messages
 */
class TemplateWhatsAppMessage extends WhatsAppMessage
{
    /**
     * @param  string  $name  The template name
     * @param  string  $language  The language code (e.g., 'en_US')
     * @param  array<TemplateComponent>  $components  The template components
     */
    public function __construct(
        protected string $name,
        protected string $language,
        protected array $components = []
    ) {}

    /**
     * Get the message type
     */
    public function getType(): WhatsAppMessageType
    {
        return WhatsAppMessageType::TEMPLATE;
    }

    /**
     * Format the message payload for the WhatsApp API
     */
    public function formatPayload(): array
    {
        $componentsArray = array_map(function (TemplateComponent $component) {
            return $component->toArray();
        }, $this->components);

        return [
            'name' => $this->name,
            'language' => [
                'code' => $this->language,
            ],
            'components' => $componentsArray,
        ];
    }

    /**
     * Add a component to the template
     *
     * @param  TemplateComponent  $component  The component to add
     */
    public function addComponent(TemplateComponent $component): self
    {
        $this->components[] = $component;

        return $this;
    }

    /**
     * Create a template message with the given parameters
     *
     * @param  string  $name  The template name
     * @param  string  $language  The language code (e.g., 'en_US')
     * @param  array<TemplateComponent>  $components  The template components
     */
    public static function create(string $name, string $language, array $components = []): self
    {
        return new self($name, $language, $components);
    }
}
