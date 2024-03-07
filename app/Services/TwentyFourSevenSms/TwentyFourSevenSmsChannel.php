<?php

namespace App\Services\TwentyFourSevenSms;

use App\Notifications\Channels\Concerns\Smsable;
use App\Settings\ServiceConfig;
use App\Settings\SmsSettings;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TwentyFourSevenSmsChannel
{
    use Smsable;

    public const NAME = 'twenty_four_seven_sms';

    public function send(object $notifiable, Notification $notification): ?string
    {
        $route = $this->getSmsRoute(
            notifiable: $notifiable,
            notification: $notification,
            channel: static::NAME,
        );

        if (blank($route) || ! phone($route)->isValid()) {
            return null;
        }

        $sms = $this->getSmsMessage(
            notifiable: $notifiable,
            notification: $notification,
            channel: static::NAME,
        );

        if (is_string($sms)) {
            $sms = new SmsMessage(message: $sms);
        }

        if (! $sms instanceof SmsMessage) {
            return null;
        }

        try {
            $serviceConfig = app(abstract: ServiceConfig::class);

            $response = Http::get(
                url: 'https://smsapi.24x7sms.com/api_2.0/Send'.($sms->isUnicode() ? 'Unicode' : '').'SMS.aspx',
                query: [
                    'APIKEY' => $serviceConfig->twenty_four_seven_sms->api_key,
                    'SenderID' => $sms->getSenderId() ?: $serviceConfig->twenty_four_seven_sms->sender_id,
                    'ServiceName' => $sms->getServiceName()->name,

                    'MobileNo' => str($route)->replace(search: '+', replace: '')->toString(),
                    'Message' => $sms->getMessage(),
                ],
            )->body();

            if (! Str::startsWith(haystack: $response, needles: 'MsgID')) {
                throw new Exception(message: 'Invalid response received. '.$response);
            }

            Log::info(message: '[24x7SMS] SendSMS Response: '.$response);

            SmsMessageSent::dispatch($notifiable, $notification, $response);

            return $response;
        } catch (Throwable $e) {
            Log::error(message: "[24x7SMS] SendSMS Error: {$e->getMessage()} | {$sms->getMessage()}");

            return null;
        }
    }
}
