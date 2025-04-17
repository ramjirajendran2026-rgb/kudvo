<?php

namespace App\Services\WhatsApp\Http;

use App\Enums\WhatsAppMessageStatus;
use App\Enums\WhatsAppMessageType;
use App\Services\WhatsApp\Data\SendWhatsAppMessageResponseData;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;

class WhatsAppClient
{
    protected Client $httpClient;

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

        $this->httpClient = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Send a WhatsApp message
     *
     * @param  string  $to  The recipient's phone number
     * @param  mixed  $message  The message content
     *
     * @throws Exception
     */
    public function sendMessage(string $to, mixed $message): SendWhatsAppMessageResponseData
    {
        try {
            // Format the phone number to E.164 format
            $to = $this->formatPhoneNumber($to);

            // Determine message type and format payload
            $payload = $this->formatMessagePayload($to, $message);

            // Send the request
            $response = $this->httpClient->post(
                "/{$this->version}/{$this->phoneNumberId}/messages",
                ['json' => $payload]
            );

            $responseData = json_decode($response->getBody()->getContents(), true);

            return SendWhatsAppMessageResponseData::from([
                'messaging_product' => $responseData['messaging_product'] ?? null,
                'contacts' => $responseData['contacts'] ?? null,
                'messages' => $responseData['messages'] ?? null,
                'status' => WhatsAppMessageStatus::SENT,
                'message_id' => $responseData['messages'][0]['id'] ?? null,
            ]);
        } catch (GuzzleException $e) {
            $errorResponse = $e->getResponse();
            $errorData = null;

            if ($errorResponse) {
                $errorData = json_decode($errorResponse->getBody()->getContents(), true);
            }

            return SendWhatsAppMessageResponseData::from([
                'status' => WhatsAppMessageStatus::FAILED,
                'error_code' => $errorData['error']['code'] ?? $e->getCode(),
                'error_message' => $errorData['error']['message'] ?? $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return SendWhatsAppMessageResponseData::from([
                'status' => WhatsAppMessageStatus::FAILED,
                'error_code' => 'unknown',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format the phone number to E.164 format
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Ensure it starts with a plus sign
        if (substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Format the message payload based on the message type
     *
     * @throws Exception
     */
    protected function formatMessagePayload(string $to, mixed $message): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
        ];

        // If message is a string, treat it as a text message
        if (is_string($message)) {
            $messageType = WhatsAppMessageType::TEXT;
            $payload['type'] = $messageType->value;
            $payload[$messageType->value] = $messageType->formatPayload($message);

            return $payload;
        }

        // If message is an array, check for type
        if (is_array($message) && isset($message['type'])) {
            $type = $message['type'];

            // Find the enum case for this type
            foreach (WhatsAppMessageType::cases() as $case) {
                if ($case->value === $type) {
                    $messageType = $case;

                    break;
                }
            }

            if (! isset($messageType)) {
                throw new Exception("Unsupported message type: {$type}");
            }

            $payload['type'] = $messageType->value;
            $payload[$messageType->value] = $messageType->formatPayload($message);

            return $payload;
        }

        throw new Exception('Invalid message format');
    }
}
