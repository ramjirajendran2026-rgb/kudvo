<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppMessage;
use App\Notifications\Channels\Concerns\Whatsappable;
use App\Notifications\Contracts\HasWhatsAppMessagePurpose;
use App\Services\WhatsApp\Data\SendWhatsAppMessageResponseData;
use App\Services\WhatsApp\Http\WhatsAppClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppChannel
{
    use Whatsappable;

    const NAME = 'whatsapp';

    public function __construct(protected WhatsAppClient $client) {}

    public function send(object $notifiable, Notification $notification): ?SendWhatsAppMessageResponseData
    {
        if (blank($route = $this->getWhatsAppRoute($notifiable, $notification)) || ! phone($route)->isValid()) {
            return null;
        }

        $message = $this->getWhatsAppMessage($notifiable, $notification);

        if (is_null($message)) {
            return null;
        }

        try {
            Log::info('[WhatsApp] SendMessage Request: ' . json_encode($message->toArray()));

            if (App::isLocal()) {
                Log::info('[WhatsApp] SendMessage Request: skipped on local');

                // return null;
            }

            $response = $this->client->sendMessage($route, $message);

            Log::info('[WhatsApp] SendMessage Response: ' . json_encode($response));

            // Store the message in the database for tracking
            $whatsAppMessage = new WhatsAppMessage([
                'purpose' => $notification instanceof HasWhatsAppMessagePurpose
                    ? $notification->getWhatsAppMessagePurpose($notifiable)
                    : null,
                'phone' => $route,
                'status' => $response->status,
                'message_id' => $response->message_id,
                'message_status' => $response->message_status,
                'message_type' => $message->type ?? null,
                'message_meta' => [
                    'request' => $message->toArray(),
                    'response' => $response->toArray(),
                ],
                'notes' => 'Outgoing WhatsApp message: ' . json_encode($message->toArray()),
            ]);

            // Set polymorphic relationship if notifiable is a model
            if (method_exists($notifiable, 'getMorphClass')) {
                $whatsAppMessage->whatsappable_type = $notifiable->getMorphClass();
                $whatsAppMessage->whatsappable_id = $notifiable->getKey();
            }

            $whatsAppMessage->save();

            WhatsAppMessageSent::dispatch($notifiable, $notification, $response);

            return $response;
        } catch (Throwable $e) {
            Log::error("[WhatsApp] SendMessage Error: {$e->getMessage()}");

            return null;
        }
    }
}
