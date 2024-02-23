<?php

namespace App\Services\Clicksend;

use App\Notifications\Channels\Concerns\Smsable;
use App\Services\Clicksend\Data\SendSmsResponseData;
use ClickSend\Api\SMSApi;
use ClickSend\ApiException;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClicksendChannel
{
    use Smsable;

    const NAME = 'clicksend';

    public function __construct(protected SMSApi $api)
    {
    }

    public function send(object $notifiable, Notification $notification): ?SendSmsResponseData
    {
        if (blank($route = $this->getSmsRoute($notifiable, $notification, self::NAME))) {
            return null;
        }

        $message = $this->getSmsMessage($notifiable, $notification, self::NAME);

        if (is_string($message)) {
            $message = (new SmsMessage())
                ->setBody($message);
        }

        if (! $message instanceof SmsMessage) {
            return null;
        }

        $message->setTo($route);

        try {
            Log::info("[Clicksend] SendSMS Request: ".((string) $message));
            $response = $this->api->smsSendPost(
                (new SmsMessageCollection())
                    ->setMessages([$message])
            );

            if (! json_validate($response)) {
                throw new Exception('Invalid response received. '.$response);
            }

            Log::info("[Clicksend] SendSMS Response: $response");

            $responseData = SendSmsResponseData::from(json_decode($response, associative: true)['data']);

            SmsSent::dispatch($responseData);

            return $responseData;
        } catch (ApiException|Throwable $e) {
            Log::error("[Clicksend] SendSMS Error: {$e->getMessage()}");

            return null;
        }
    }
}
