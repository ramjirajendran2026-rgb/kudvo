<?php

namespace App\Services\Clicksend\Actions;

use App\Enums\SmsMessageStatus;

class GetSmsStatusForProviderStatus
{
    public function execute(string $providerStatus): SmsMessageStatus
    {
        return match (strtolower($providerStatus)) {
            'success' => SmsMessageStatus::PENDING,
            'delivered' => SmsMessageStatus::DELIVERED,
            'undelivered' => SmsMessageStatus::FAILED,
            default => SmsMessageStatus::UNKNOWN,
        };
    }
}
