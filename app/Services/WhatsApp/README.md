# WhatsApp Cloud API Integration

This directory contains the implementation of the WhatsApp Cloud API integration for sending WhatsApp messages.

## Configuration

Add the following environment variables to your `.env` file:

```
WHATSAPP_API_URL=https://graph.facebook.com
WHATSAPP_ACCESS_TOKEN=your_access_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_API_VERSION=v18.0
```

## Usage

### Sending a Simple Text Message

```php
use Illuminate\Support\Facades\Notification;
use App\Notifications\WhatsAppNotification;

// Send to a user
$user->notify(new WhatsAppNotification('Hello from WhatsApp!'));

// Or send directly
Notification::route('whatsapp', '+1234567890')
    ->notify(new WhatsAppNotification('Hello from WhatsApp!'));
```

### Creating a WhatsApp Notification

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WhatsAppNotification extends Notification
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsapp($notifiable)
    {
        return $this->message;
    }
}
```

### Sending Different Types of Messages

```php
use App\Enums\WhatsAppMessageType;

// Text message with preview URL
$message = [
    'type' => WhatsAppMessageType::TEXT->value,
    'preview_url' => true,
    'body' => 'Check out this link: https://example.com',
];

// Image message
$message = [
    'type' => WhatsAppMessageType::IMAGE->value,
    'link' => 'https://example.com/image.jpg',
    'caption' => 'Optional caption',
];

// Document message
$message = [
    'type' => WhatsAppMessageType::DOCUMENT->value,
    'link' => 'https://example.com/document.pdf',
    'caption' => 'Optional caption',
    'filename' => 'document.pdf',
];

// Template message
$message = [
    'type' => WhatsAppMessageType::TEMPLATE->value,
    'template' => [
        'name' => 'template_name',
        'language' => [
            'code' => 'en_US',
        ],
        'components' => [
            [
                'type' => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => 'Parameter value',
                    ],
                ],
            ],
        ],
    ],
];

$notifiable->notify(new WhatsAppNotification($message));
```

## Message Types

The following message types are supported:

-   `TEXT`: Text messages
-   `IMAGE`: Image messages
-   `AUDIO`: Audio messages
-   `DOCUMENT`: Document messages
-   `VIDEO`: Video messages
-   `STICKER`: Sticker messages
-   `LOCATION`: Location messages
-   `CONTACT`: Contact messages
-   `INTERACTIVE`: Interactive messages (buttons, lists)
-   `TEMPLATE`: Template messages
-   `REACTION`: Reaction messages

See the `WhatsAppMessageType` enum for all available message types.

## Error Handling

The WhatsApp client will return a `SendWhatsAppMessageResponseData` object with error information if the message fails to send. You can check the `status` property to determine if the message was sent successfully.

```php
use App\Enums\WhatsAppMessageStatus;

$response = $whatsAppClient->sendMessage($to, $message);

if ($response->status === WhatsAppMessageStatus::FAILED) {
    // Handle error
    $errorCode = $response->error_code;
    $errorMessage = $response->error_message;
}
```
