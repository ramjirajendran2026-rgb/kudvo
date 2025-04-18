# WhatsApp Messages

This directory contains classes for creating and formatting different types of WhatsApp messages.

## Overview

The WhatsApp API supports various message types, such as text, template, media (image, audio, video, document, sticker), location, contact, interactive, and reaction messages. Each message type has its own format and requirements.

This implementation provides an object-oriented approach to creating and formatting WhatsApp messages, with a separate class for each message type.

## Class Hierarchy

-   `WhatsAppMessage` (abstract base class)
    -   `TextWhatsAppMessage`
    -   `TemplateWhatsAppMessage`
    -   `MediaWhatsAppMessage` (abstract base class for media messages)
        -   `ImageWhatsAppMessage`
        -   `AudioWhatsAppMessage`
        -   `VideoWhatsAppMessage`
        -   `DocumentWhatsAppMessage`
        -   `StickerWhatsAppMessage`
    -   `LocationWhatsAppMessage`
    -   `ContactWhatsAppMessage`
    -   `InteractiveWhatsAppMessage`
    -   `ReactionWhatsAppMessage`

## Usage

### Using the Factory

The easiest way to create WhatsApp messages is to use the `WhatsAppMessageFactory` class:

```php
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;

// Create a text message
$textMessage = WhatsAppMessageFactory::text('Hello, world!');

// Create a template message
$templateMessage = WhatsAppMessageFactory::template(
    'sample_template',
    'en_US',
    [
        [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => 'John Doe'
                ]
            ]
        ]
    ]
);

// Create an image message
$imageMessage = WhatsAppMessageFactory::image(
    null,
    'https://example.com/image.jpg',
    'Check out this image!'
);

// Create a location message
$locationMessage = WhatsAppMessageFactory::location(
    37.7749,
    -122.4194,
    'San Francisco',
    '123 Main St, San Francisco, CA'
);
```

### Creating Messages Directly

You can also create message objects directly:

```php
use App\Services\WhatsApp\Messages\TextWhatsAppMessage;
use App\Services\WhatsApp\Messages\TemplateWhatsAppMessage;

// Create a text message
$textMessage = new TextWhatsAppMessage('Hello, world!');

// Create a template message
$templateMessage = TemplateWhatsAppMessage::create(
    'sample_template',
    'en_US',
    [
        [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => 'John Doe'
                ]
            ]
        ]
    ]
);
```

### Sending Messages

To send a message, pass the message object to the `WhatsAppClient::sendMessage` method:

```php
use App\Services\WhatsApp\Http\WhatsAppClient;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;

$client = app(WhatsAppClient::class);
$message = WhatsAppMessageFactory::text('Hello, world!');
$response = $client->sendMessage('+1234567890', $message);
```

## Legacy Format Support

The `WhatsAppClient` still supports the legacy format for backward compatibility:

```php
$client->sendMessage('+1234567890', 'Hello, world!'); // String message
$client->sendMessage('+1234567890', [
    'type' => 'text',
    'body' => 'Hello, world!'
]); // Array message
```

However, it's recommended to use the new message classes for better type safety and code organization.
