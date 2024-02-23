<?php

namespace App\Services\Clicksend\Actions;

use App\Models\SmsMessage;
use App\Services\Clicksend\ClicksendChannel;
use App\Services\Clicksend\Data\SmsReceiptData;

class ProcessWebhookPayload
{
    public function execute(SmsReceiptData $receiptData)
    {
        $smsMessage = SmsMessage::whereNotNull('provider_message_id')
            ->where('provider_message_id', $receiptData->message_id)
            ->where('provider', ClicksendChannel::NAME)
            ->sole();

        if (blank($smsMessage)) {
            return null;
        }

        $smsMessage->fill([
            'status' => app(GetSmsStatusForProviderStatus::class)->execute($receiptData->status),
            'notes' => $receiptData->status_text,
            'provider_status' => $receiptData->status,
        ]);
        $smsMessage->provider_meta ??= [];
        $smsMessage->provider_meta = array_merge(
            $smsMessage->provider_meta['payloads'] ?? [],
            [$receiptData->toArray()]
        );

        if ($smsMessage->isDirty()) {
            $smsMessage->save();
        }
    }
}
