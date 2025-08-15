<?php

namespace App\Services\TwentyFourSevenSms\Actions;

use App\Services\TwentyFourSevenSms\ServiceName;
use App\Settings\ServiceConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetBalance
{
    public function __construct(protected ServiceConfig $serviceConfig) {}

    /**
     * @throws Throwable
     */
    public function execute(): string
    {
        try {
            $serviceConfig = $this->serviceConfig;

            return Http::get(
                url: 'https://smsapi.24x7sms.com/api_2.0/BalanceCheck.aspx',
                query: [
                    'APIKEY' => $serviceConfig->twenty_four_seven_sms->api_key,
                    'ServiceName' => ServiceName::TEMPLATE_BASED->name,
                ],
            )->body();
        } catch (Throwable $e) {
            Log::info(message: "[24x7SMS] GetBalance Error: {$e->getMessage()}");

            throw $e;
        }
    }
}
