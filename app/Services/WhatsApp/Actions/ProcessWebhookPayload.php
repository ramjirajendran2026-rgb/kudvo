<?php

namespace App\Services\WhatsApp\Actions;

use App\Enums\WhatsAppMessageStatus;
use App\Models\WhatsAppMessage;
use App\Services\WhatsApp\Data\WhatsAppReceiptData;
use Illuminate\Support\Facades\Log;

class ProcessWebhookPayload
{
    public function execute(WhatsAppReceiptData $receiptData)
    {
        // Process message status updates
        if (filled($receiptData->statuses) && count($receiptData->statuses) > 0) {
            foreach ($receiptData->statuses as $status) {
                if (empty($status['id'])) {
                    continue;
                }

                $this->processStatusUpdate($status);
            }
        }

        // Process incoming messages
        if (filled($receiptData->messages) && count($receiptData->messages) > 0) {
            foreach ($receiptData->messages as $message) {
                if (empty($message['id'])) {
                    continue;
                }

                $this->processIncomingMessage($message, $receiptData);
            }
        }
    }

    protected function processStatusUpdate(array $status)
    {
        $messageId = $status['id'] ?? null;
        $statusValue = $status['status'] ?? null;

        if (blank($messageId) || blank($statusValue)) {
            return null;
        }

        Log::info("[WhatsApp] Status Update: Message ID: {$messageId}, Status: {$statusValue}");

        $whatsAppMessage = WhatsAppMessage::whereNotNull('message_id')
            ->where('message_id', $messageId)
            ->first();

        if (blank($whatsAppMessage)) {
            Log::warning("[WhatsApp] Message not found for ID: {$messageId}");

            return null;
        }

        $whatsAppStatus = match ($statusValue) {
            'sent' => WhatsAppMessageStatus::Sent,
            'delivered' => WhatsAppMessageStatus::Delivered,
            'read' => WhatsAppMessageStatus::Read,
            'failed' => WhatsAppMessageStatus::Failed,
            default => WhatsAppMessageStatus::Unknown,
        };

        $whatsAppMessage->fill([
            'status' => $whatsAppStatus,
            'message_status' => $statusValue,
            'notes' => $status['error']['message'] ?? null,
        ]);
        $whatsAppMessage->message_meta ??= [];
        $whatsAppMessage->message_meta = array_merge(
            $whatsAppMessage->message_meta,
            ['status_updates' => array_merge($whatsAppMessage->message_meta['status_updates'] ?? [], [$status])]
        );

        if ($whatsAppMessage->isDirty()) {
            $whatsAppMessage->save();
        }

        return $whatsAppMessage;
    }

    protected function processIncomingMessage(array $message, WhatsAppReceiptData $receiptData)
    {
        $messageId = $message['id'] ?? null;
        $from = $message['from'] ?? null;
        $type = $message['type'] ?? null;
        $timestamp = $receiptData->timestamp;

        if (blank($messageId) || blank($from) || blank($type)) {
            return null;
        }

        Log::info("[WhatsApp] Incoming Message: From: {$from}, Type: {$type}, ID: {$messageId}");

        // Extract message content based on type
        $content = match ($type) {
            'text' => $message['text']['body'] ?? null,
            'image' => 'Image: ' . ($message['image']['caption'] ?? 'No caption'),
            'audio' => 'Audio message',
            'document' => 'Document: ' . ($message['document']['filename'] ?? 'No filename'),
            'video' => 'Video: ' . ($message['video']['caption'] ?? 'No caption'),
            'location' => 'Location: ' . ($message['location']['name'] ?? 'No name'),
            'contacts' => 'Contact shared',
            'interactive' => 'Interactive message',
            default => "Unknown message type: {$type}",
        };

        // Create a new WhatsAppMessage record for the incoming message
        $whatsAppMessage = new WhatsAppMessage([
            'purpose' => null, // Incoming messages don't have a purpose
            'phone' => $from,
            'status' => WhatsAppMessageStatus::Delivered, // Incoming messages are already delivered
            'message_id' => $messageId,
            'message_status' => 'received',
            'message_type' => $type,
            'message_meta' => [
                'message' => $message,
                'receipt' => $receiptData->toArray(),
            ],
            'notes' => "Incoming {$type} message: {$content}",
        ]);

        $whatsAppMessage->save();

        return $whatsAppMessage;
    }
}
