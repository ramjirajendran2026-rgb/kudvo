<?php

namespace App\Services\WhatsApp\Messages\TemplateComponents;

use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\CurrencyParameter;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\DateTimeParameter;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\TemplateParameter;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\TextParameter;
use DateTime;

/**
 * Factory class for creating template components
 */
class TemplateComponentFactory
{
    /**
     * Create a header component
     *
     * @param  array<TemplateParameter>  $parameters  The component parameters
     */
    public static function header(array $parameters = []): HeaderComponent
    {
        return new HeaderComponent($parameters);
    }

    /**
     * Create a body component
     *
     * @param  array<TemplateParameter>  $parameters  The component parameters
     */
    public static function body(array $parameters = []): BodyComponent
    {
        return new BodyComponent($parameters);
    }

    /**
     * Create a quick reply button component
     *
     * @param  string  $payload  The button payload
     * @param  string|null  $text  Optional button text
     */
    public static function quickReplyButton(string $payload, ?string $text = null): ButtonComponent
    {
        return ButtonComponent::quickReply($payload, $text);
    }

    /**
     * Create a URL button component
     *
     * @param  string  $text  The button URL
     */
    public static function urlButton(string $text): ButtonComponent
    {
        return ButtonComponent::url($text);
    }

    /**
     * Create a Flow button component
     */
    public static function flowButton(): ButtonComponent
    {
        return ButtonComponent::flow();
    }

    /**
     * Create a text parameter
     *
     * @param  string  $text  The text value
     */
    public static function textParameter(string $text, ?string $name = null): TextParameter
    {
        return new TextParameter($text, $name);
    }

    /**
     * Create a currency parameter
     *
     * @param  string  $currencyCode  The currency code (e.g., 'USD')
     * @param  string  $amount  The amount as a string
     * @param  string|null  $fallbackValue  Optional fallback value
     */
    public static function currencyParameter(string $currencyCode, string $amount, ?string $fallbackValue = null): CurrencyParameter
    {
        return new CurrencyParameter($currencyCode, $amount, $fallbackValue);
    }

    /**
     * Create a date time parameter
     *
     * @param  string  $fallbackValue  The fallback value
     * @param  DateTime|null  $dateTime  Optional DateTime object
     */
    public static function dateTimeParameter(string $fallbackValue, ?DateTime $dateTime = null): DateTimeParameter
    {
        return new DateTimeParameter($fallbackValue, $dateTime);
    }
}
