<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\Nomination;
use App\Models\OneTimePassword;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
        $domain = request()->getHost();

        return <<<EOD
Your OTP verification code is {$this->oneTimePassword->code}

-iNodesys
@$domain #{$this->oneTimePassword->code}
EOD;

    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
