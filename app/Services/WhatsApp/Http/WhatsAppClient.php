<?php

namespace App\Services\WhatsApp\Http;

use App\Enums\WhatsAppMessageStatus;
use App\Services\WhatsApp\Data\SendWhatsAppMessageResponseData;
use App\Services\WhatsApp\Messages\WhatsAppMessage;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class WhatsAppClient
{
    protected string $apiUrl;

    protected string $accessToken;

    protected string $phoneNumberId;

    protected string $version;

    public function __construct()
    {
        $this->apiUrl = Config::get('services.whatsapp.api_url', 'https://graph.facebook.com');
        $this->accessToken = Config::get('services.whatsapp.access_token');
        $this->phoneNumberId = Config::get('services.whatsapp.phone_number_id');
        $this->version = Config::get('services.whatsapp.version', 'v18.0');
    }

    public function sendMessage(string $to, string | WhatsAppMessage $message): SendWhatsAppMessageResponseData
    {
        $to = $this->formatPhoneNumber($to);

        $payload = $this->formatMessagePayload($to, $message);

        $response = Http::asJson()
            ->withToken($this->accessToken)
            ->baseUrl($this->apiUrl)
            ->post("/{$this->version}/{$this->phoneNumberId}/messages", $payload);

        if ($response->failed()) {
            return new SendWhatsAppMessageResponseData(
                status: WhatsAppMessageStatus::Failed,
                error_code: $response->status(),
                error_message: $response->body(),
            );
        }

        if (blank($response->json())) {
            return new SendWhatsAppMessageResponseData(
                status: WhatsAppMessageStatus::Failed,
                error_code: 'unknown',
                error_message: 'Invalid response from WhatsApp API',
            );
        }

        return new SendWhatsAppMessageResponseData(
            messaging_product: $response->json('messaging_product'),
            contacts: $response->json('contacts'),
            messages: $response->json('messages'),
            status: WhatsAppMessageStatus::Sent,
            message_id: $response->json('messages.0.id'),
            message_status: $response->json('messages.0.status'),
        );
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        return str($phoneNumber)
            ->replaceMatches('/[^0-9]/', '')
            ->prepend('+');
    }

    protected function formatMessagePayload(string $to, string | WhatsAppMessage $message): array
    {
        if (is_string($message)) {
            $message = WhatsAppMessageFactory::text($message);
        }

        $payload = $message->toArray();
        $payload['to'] = $to;

        return $payload;
    }
}
