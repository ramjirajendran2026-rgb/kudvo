<?php

namespace App\Listeners;

use App\Models\SmsMessage;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Services\Clicksend\Actions\GetSmsStatusForProviderStatus;
use App\Services\Clicksend\ClicksendChannel;
use App\Services\Clicksend\Data\SendSmsResponseMessageData;
use App\Services\Clicksend\SmsSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LogClicksendSentSms
{
    public function handle(SmsSent $event): void
    {
        if (blank($event->response->messages)) {
            return;
        }

        /** @var SendSmsResponseMessageData $message */
        foreach ($event->response->messages as $message) {
            Log::info("[Clicksend] Message Response: {$message->toJson()}");

            if (blank($message->message_id)) {
                continue;
            }

            SmsMessage::createOrFirst(
                [
                    'provider' => ClicksendChannel::NAME,
                    'provider_message_id' => $message->message_id,
                ],
                [
                    'purpose' => $event->notification instanceof HasSmsMessagePurpose
                        ? $event->notification->getSmsMessagePurpose(notifiable: $event->notifiable)
                        : null,
                    'phone' => $message->to,
                    'status' => app(GetSmsStatusForProviderStatus::class)->execute($message->status),
                    'provider_status' => $message->status,
                    'provider_meta' => [
                        'response' => $message->toArray(),
                    ],

                    ...$event->notifiable instanceof Model ?
                        [
                            'smsable_type' => $event->notifiable->getMorphClass(),
                            'smsable_id' => $event->notifiable->getKey(),
                        ] :
                        [],
                ]
            );
        }
    }
}
