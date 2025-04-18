<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents;

use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\TemplateParameter;

/**
 * Class for body template components
 */
class BodyComponent extends TemplateComponent
{
    /**
     * @param  array<TemplateParameter>  $parameters  The component parameters
     */
    public function __construct(
        protected array $parameters = []
    ) {}

    /**
     * Get the component type
     */
    public function getType(): string
    {
        return 'body';
    }

    /**
     * Get the component parameters
     */
    public function getParameters(): array
    {
        return array_map(function (TemplateParameter $parameter) {
            return $parameter->toArray();
        }, $this->parameters);
    }

    /**
     * Add a parameter to the component
     */
    public function addParameter(TemplateParameter $parameter): self
    {
        $this->parameters[] = $parameter;

        return $this;
    }
}
