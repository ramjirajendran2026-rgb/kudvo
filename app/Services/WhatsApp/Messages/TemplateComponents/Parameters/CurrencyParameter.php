<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents\Parameters;

/**
 * Class for currency template parameters
 */
class CurrencyParameter extends TemplateParameter
{
    /**
     * @param  string  $currencyCode  The currency code (e.g., 'USD')
     * @param  string  $amount  The amount as a string
     * @param  string|null  $fallbackValue  Optional fallback value
     */
    public function __construct(
        protected string $currencyCode,
        protected string $amount,
        protected ?string $fallbackValue = null
    ) {}

    /**
     * Get the parameter type
     */
    public function getType(): string
    {
        return 'currency';
    }

    /**
     * Convert the parameter to an array for the WhatsApp API
     */
    public function toArray(): array
    {
        $currency = [
            'code' => $this->currencyCode,
            'amount_1000' => $this->amount,
        ];

        if ($this->fallbackValue !== null) {
            $currency['fallback_value'] = $this->fallbackValue;
        }

        return [
            'type' => $this->getType(),
            'currency' => $currency,
        ];
    }
}
