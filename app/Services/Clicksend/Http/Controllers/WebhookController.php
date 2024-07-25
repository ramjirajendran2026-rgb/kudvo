<?php

namespace App\Services\Clicksend\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Clicksend\Actions\ProcessWebhookPayload;
use App\Services\Clicksend\Data\SmsReceiptData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('[Clicksend] WebhookPayload: ' . json_encode($request->all()));

        $payloadData = SmsReceiptData::from($request->all());

        if (filled($payloadData->message_id)) {
            app(ProcessWebhookPayload::class)->execute($payloadData);
        }

        return response()->noContent();
    }
}
