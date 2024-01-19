<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\Nomination;
use App\Models\OneTimePassword;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ElectionMfaNotification extends Notification
{
    public function __construct(
        public Election $election,
        public OneTimePassword $oneTimePassword
    )
    {
    }

    public function via(object $notifiable): array
    {
        return [
            TwentyFourSevenSmsChannel::class,
            'mail',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nomination = $this->election;
        $oneTimePassword = $this->oneTimePassword;

        return (new MailMessage)
            ->subject(subject: "MFA Code for $nomination->name")
            ->line(line: "**$oneTimePassword->code** is your MFA code for $nomination->name");
    }

    public function toSms(object $notifiable): string
    {
        return <<<EOD
Your OTP verification code is {$this->oneTimePassword->code}

@kudvo.com #{$this->oneTimePassword->code}
EOD;

    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
