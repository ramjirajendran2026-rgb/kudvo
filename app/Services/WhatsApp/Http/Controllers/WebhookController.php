<?php

namespace App\Services\WhatsApp\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\Actions\ProcessWebhookPayload;
use App\Services\WhatsApp\Data\WhatsAppReceiptData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // Handle webhook verification
        if ($request->isMethod('get')) {
            $mode = $request->input('hub_mode');
            $token = $request->input('hub_verify_token');
            $challenge = $request->input('hub_challenge');

            Log::info('[WhatsApp] Webhook Verification Request', [
                'mode' => $mode,
                'token' => $token,
                'challenge' => $challenge,
            ]);

            // Verify token (should be configured in .env)
            $verifyToken = config('services.whatsapp.webhook_verify_token');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('[WhatsApp] Webhook Verification Successful');
                return response($challenge);
            }

            Log::warning('[WhatsApp] Webhook Verification Failed', [
                'expected_token' => $verifyToken,
                'received_token' => $token,
            ]);

            return response()->json(['error' => 'Verification failed'], 403);
        }

        // Handle webhook payload (POST request)
        Log::info('[WhatsApp] WebhookPayload: ' . json_encode($request->all()));

        $payloadData = WhatsAppReceiptData::from($request->all());

        if (filled($payloadData->message_id) || (filled($payloadData->statuses) && count($payloadData->statuses) > 0)) {
            app(ProcessWebhookPayload::class)->execute($payloadData);
        }

        return response()->noContent();
    }
}
