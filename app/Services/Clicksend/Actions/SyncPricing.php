<?php

namespace App\Services\Clicksend\Actions;

use App\Enums\SmsChannel;
use App\Models\SmsPrice;
use App\Settings\ServiceConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncPricing
{
    public function __construct(protected ServiceConfig $serviceConfig) {}

    /**
     * @throws Throwable
     */
    public function execute(): void
    {
        try {
            $csvFileUrl = Http::withBasicAuth($this->serviceConfig->clicksend->username, $this->serviceConfig->clicksend->api_key)
                ->get('https://rest.clicksend.com/v3/pricing-export')
                ->json('data.url');

            Log::info(message: "[Clicksend] SyncPricing: $csvFileUrl");

            $csvContent = Http::get($csvFileUrl)->body();
            $lines = collect(str_getcsv(str_replace("\r", '', $csvContent), "\n"));

            $headers = str_getcsv($lines->shift());

            $lines = $lines->map(function ($line) use ($headers) {
                $row = array_combine($headers, str_getcsv($line));

                $actualPrice = ((float) $row['sms_price_rate_0']) + ((float) $row['price_sms_carrier_fee']);
                $actualPrice *= 100;
                $actualPrice = round($actualPrice);

                $margin = $actualPrice * 0.3;
                if ($margin > 50) {
                    $margin = 50;
                }

                return [
                    'country' => $row['country'] ?? null,
                    'currency' => $row['currency'] ?? null,
                    'actual_price' => $actualPrice,
                    'margin' => round($margin),
                    'channel' => SmsChannel::Clicksend,
                ];
            })->whereNotNull('country')->whereNotNull('currency');

            SmsPrice::upsert($lines->toArray(), ['country', 'currency', 'channel']);

            Log::info(message: "[Clicksend] Price updated for {$lines->count()} countries");
        } catch (Throwable $e) {
            Log::info(message: "[Clicksend] SyncPricing Error: {$e->getMessage()}");

            throw $e;
        }
    }
}
