<?php

namespace App\Listeners;

use App\Actions\ParseMailMessageId;
use App\Models\Email;
use App\Models\OneTimePassword;
use App\Notifications\Contracts\HasMailMessagePurpose;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;

class LogSentNotification
{
    public function handle(NotificationSent $event): void
    {
        if ($event->channel == 'mail' && $event->response instanceof SentMessage) {
            $this->attachNotificationToEmail(
                notification: $event->notification,
                sentMessage: $event->response,
                notifiable: $event->notifiable,
            );
        }
    }

    protected function attachNotificationToEmail(
        Notification $notification,
        SentMessage $sentMessage,
        object $notifiable,
    ): void {
        $messageId = app(abstract: ParseMailMessageId::class)
            ->execute($sentMessage);

        if (
            blank($messageId)
            || blank($email = Email::where('message_id', $messageId)->sole())
        ) {
            return;
        }

        if ($notifiable instanceof OneTimePassword) {
            $email->notifiable_type = $notifiable->relatable_type;
            $email->notifiable_id = $notifiable->relatable_id;
        } elseif ($notifiable instanceof Model) {
            $email->notifiable_type = $notifiable->getMorphClass();
            $email->notifiable_id = $notifiable->getKey();
        }

        if ($notification instanceof HasMailMessagePurpose) {
            $email->purpose = $notification->getMailMessagePurpose(notifiable: $notifiable);
        }

        if ($email->isDirty()) {
            $email->save();
        }
    }
}
