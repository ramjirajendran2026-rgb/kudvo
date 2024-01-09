<?php

namespace App\Notifications;

use App\Models\Nomination;
use App\Models\OneTimePassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NominationMfaNotification extends Notification
{
    public function __construct(
        public Nomination $nomination,
        public OneTimePassword $oneTimePassword
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $nomination = $this->nomination;
        $oneTimePassword = $this->oneTimePassword;

        return (new MailMessage)
            ->subject(subject: "MFA Code for $nomination->name")
            ->line(line: "**$oneTimePassword->code** is your MFA code for $nomination->name");
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
