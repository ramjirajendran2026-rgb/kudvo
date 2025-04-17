<?php

namespace App\Services\WhatsApp;

use App\Notifications\Channels\Concerns\Smsable;
use App\Services\WhatsApp\Data\SendWhatsAppMessageResponseData;
use App\Services\WhatsApp\Http\WhatsAppClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppChannel
{
    use Smsable;

    const NAME = 'whatsapp';

    public function __construct(protected WhatsAppClient $client) {}

    public function send(object $notifiable, Notification $notification): ?SendWhatsAppMessageResponseData
    {
        if (blank($route = $this->getSmsRoute($notifiable, $notification, self::NAME)) || ! phone($route)->isValid()) {
            return null;
        }

        $message = $this->getSmsMessage($notifiable, $notification, self::NAME);

        if (is_null($message)) {
            return null;
        }

        try {
            Log::info('[WhatsApp] SendMessage Request: ' . json_encode($message));

            if (App::isLocal()) {
                Log::info('[WhatsApp] SendMessage Request: skipped on local');

                return null;
            }

            $response = $this->client->sendMessage($route, $message);

            Log::info('[WhatsApp] SendMessage Response: ' . json_encode($response));

            WhatsAppMessageSent::dispatch($notifiable, $notification, $response);

            return $response;
        } catch (Throwable $e) {
            Log::error("[WhatsApp] SendMessage Error: {$e->getMessage()}");

            return null;
        }
    }
}
