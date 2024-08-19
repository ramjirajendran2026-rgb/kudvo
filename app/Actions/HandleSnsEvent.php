<?php

namespace App\Actions;

use App\Models\Email;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HandleSnsEvent
{
    public function handle(string $controllerAction, object $message): void
    {
        $eventTypes = [
            'Bounce',
            'Complaint',
            'Delivery',
            'Send',
            'Reject',
            'Open',
            'Click',
            'Rendering Failure',
            'DeliveryDelay',
            'Subscription',
        ];

        if (in_array($message->eventType, $eventTypes, true)) {
            $this->{'handle' . Str::studly($message->eventType) . 'Event'}($message);
        }
    }

    public function handleBounceEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'bounced_at' => $message->bounce->timestamp ? new Carbon($message->bounce->timestamp) : null,
                'bounce_data' => $message->bounce,
            ]);

        $this->logMessage('Bounce data was saved successfully.');
    }

    public function handleComplaintEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'complained_at' => $message->complaint->timestamp ? new Carbon($message->complaint->timestamp) : null,
                'complaint_data' => $message->complaint,
            ]);

        $this->logMessage('Complaint data was saved successfully.');
    }

    public function handleDeliveryEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'delivered_at' => $message->delivery->timestamp ? new Carbon($message->delivery->timestamp) : null,
            ]);

        $this->logMessage('Delivery data was saved successfully.');
    }

    public function handleSendEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'sent_at' => $message->mail->timestamp ? new Carbon($message->mail->timestamp) : null,
            ]);

        $this->logMessage('Send data was saved successfully.');
    }

    public function handleRejectEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'rejected_at' => $message->mail->timestamp ? new Carbon($message->mail->timestamp) : null,
                'reject_data' => $message->reject,
            ]);

        $this->logMessage('Reject data was saved successfully.');
    }

    public function handleOpenEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)->sole()
            ?->opens()->create([
                'ip_address' => $message->open->ipAddress,
                'user_agent' => $message->open->userAgent,
                'opened_at' => $message->open->timestamp ? new Carbon($message->open->timestamp) : null,
            ]);

        $this->logMessage('Open data was saved successfully.');
    }

    public function handleClickEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)->sole()
            ?->clicks()->create([
                'ip_address' => $message->click->ipAddress,
                'user_agent' => $message->click->userAgent,
                'clicked_at' => $message->click->timestamp ? new Carbon($message->click->timestamp) : null,
                'link' => $message->click->link,
                'link_tags' => $message->click->linkTags,
            ]);

        $this->logMessage('Click data was saved successfully.');
    }

    public function handleRenderingFailureEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'rendering_failed_at' => $message->mail->timestamp ? new Carbon($message->mail->timestamp) : null,
                'rendering_failure_data' => $message->failure,
            ]);

        $this->logMessage('Rendering failure data was saved successfully.');
    }

    public function handleDeliveryDelayEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'delivery_delayed_at' => $message->deliveryDelay->timestamp ? new Carbon($message->deliveryDelay->timestamp) : null,
                'delivery_delay_data' => $message->deliveryDelay,
            ]);

        $this->logMessage('Delivery delay data was saved successfully.');
    }

    public function handleSubscriptionEvent(object $message): void
    {
        Email::where('message_id', $message->mail->messageId)
            ->sole()
            ?->update(attributes: [
                'subscription_notified_at' => $message->subscription->timestamp ? new Carbon($message->subscription->timestamp) : null,
                'subscription_data' => $message->subscription,
            ]);

        $this->logMessage('Subscription data was saved successfully.');
    }

    protected function logMessage(string $message): void
    {
        if ($this->debug()) {
            Log::debug($message);
        }
    }

    protected function debug(): bool
    {
        return false;
    }
}
