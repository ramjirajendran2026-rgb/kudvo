<?php

namespace App\Listeners;

use App\Enums\SmsMessageStatus;
use App\Models\SmsMessage;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Services\TwentyFourSevenSms\SmsMessageSent;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogTwentyFourSevenSmsSentMessage
{
    public function handle(SmsMessageSent $event): void
    {
        if (blank($response = $event->result)) {
            return;
        }

        foreach (explode(separator: '<br>', string: $response) as $message) {
            if (
                blank($message)
                || ! Str::startsWith(haystack: $message, needles: 'MsgID:')
                || count($message = explode(separator: ':', string: $message)) != 5
            ) {
                continue;
            }

            Log::info(message: '[24x7SMS] Message Response: '.json_encode($message));

            SmsMessage::createOrFirst(
                [
                    'provider' => TwentyFourSevenSmsChannel::NAME,
                    'provider_message_id' => $message[1],
                ],
                [
                    'purpose' => $event->notification instanceof HasSmsMessagePurpose
                        ? $event->notification->getSmsMessagePurpose()
                        : null,
                    'phone' => '+'.$message[2],
                    'status' => SmsMessageStatus::SENT,
                    'provider_status' => $message[4],
                    'provider_meta' => [
                        'response' => $message,
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
