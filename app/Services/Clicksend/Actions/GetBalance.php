<?php

namespace App\Services\Clicksend\Actions;

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
            return Http::withBasicAuth($this->serviceConfig->clicksend->username, $this->serviceConfig->clicksend->api_key)
                ->get('https://rest.clicksend.com/v3/account')
                ->json('data.balance') ?? '--';
        } catch (Throwable $e) {
            Log::info(message: "[Clicksend] GetAccount Error: {$e->getMessage()}");

            throw $e;
        }
    }
}
