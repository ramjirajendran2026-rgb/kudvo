# WhatsApp Template Components

This directory contains classes for creating and formatting WhatsApp template message components.

## Overview

WhatsApp templates can have various components, such as header, body, and buttons. Each component can have parameters of different types, such as text, currency, and date_time.

This implementation provides an object-oriented approach to creating and formatting template components, with a separate class for each component type and parameter type.

## Class Hierarchy

-   `TemplateComponent` (abstract base class)
    -   `HeaderComponent`
    -   `BodyComponent`
    -   `ButtonComponent`
-   `TemplateParameter` (abstract base class)
    -   `TextParameter`
    -   `CurrencyParameter`
    -   `DateTimeParameter`

## Usage

### Using the Factory

The easiest way to create template components is to use the `TemplateComponentFactory` class:

```php
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponentFactory;

// Create a header component with a text parameter
$headerComponent = TemplateComponentFactory::header([
    TemplateComponentFactory::textParameter('Welcome to our service!')
]);

// Create a body component with multiple text parameters
$bodyComponent = TemplateComponentFactory::body([
    TemplateComponentFactory::textParameter('Hello, John Doe!'),
    TemplateComponentFactory::textParameter('Your order has been confirmed.')
]);

// Create a quick reply button
$quickReplyButton = TemplateComponentFactory::quickReplyButton('PAYLOAD_VIEW_ORDER', 'View Order');

// Create a URL button
$urlButton = TemplateComponentFactory::urlButton('https://example.com/order/123', 'Track Order');
```

### Creating Components Directly

You can also create component objects directly:

```php
use App\Services\WhatsApp\Messages\TemplateComponents\HeaderComponent;
use App\Services\WhatsApp\Messages\TemplateComponents\BodyComponent;
use App\Services\WhatsApp\Messages\TemplateComponents\ButtonComponent;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\TextParameter;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\CurrencyParameter;
use App\Services\WhatsApp\Messages\TemplateComponents\Parameters\DateTimeParameter;

// Create a header component
$headerComponent = new HeaderComponent([
    new TextParameter('Welcome to our service!')
]);

// Create a body component
$bodyComponent = new BodyComponent();
$bodyComponent->addParameter(new TextParameter('Hello, John Doe!'));
$bodyComponent->addParameter(new TextParameter('Your order has been confirmed.'));

// Create a currency parameter
$currencyParameter = new CurrencyParameter('USD', '1000', '$10.00');
$bodyComponent->addParameter($currencyParameter);

// Create a date time parameter
$dateTimeParameter = new DateTimeParameter('January 1, 2023', new \DateTime('2023-01-01'));
$bodyComponent->addParameter($dateTimeParameter);

// Create a quick reply button
$quickReplyButton = ButtonComponent::quickReply('PAYLOAD_VIEW_ORDER', 'View Order');

// Create a URL button
$urlButton = ButtonComponent::url('https://example.com/order/123', 'Track Order');
```

### Using Components with Template Messages

To use the components with a template message, pass them to the `TemplateWhatsAppMessage` constructor or `create` method:

```php
use App\Services\WhatsApp\Messages\TemplateWhatsAppMessage;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponentFactory;

// Create components
$headerComponent = TemplateComponentFactory::header([
    TemplateComponentFactory::textParameter('Welcome to our service!')
]);
$bodyComponent = TemplateComponentFactory::body([
    TemplateComponentFactory::textParameter('Hello, John Doe!'),
    TemplateComponentFactory::textParameter('Your order has been confirmed.')
]);
$quickReplyButton = TemplateComponentFactory::quickReplyButton('PAYLOAD_VIEW_ORDER', 'View Order');

// Create a template message with components
$templateMessage = TemplateWhatsAppMessage::create(
    'order_confirmation',
    'en_US',
    [$headerComponent, $bodyComponent, $quickReplyButton]
);

// Or add components after creation
$templateMessage = new TemplateWhatsAppMessage('order_confirmation', 'en_US');
$templateMessage->addComponent($headerComponent);
$templateMessage->addComponent($bodyComponent);
$templateMessage->addComponent($quickReplyButton);
```
