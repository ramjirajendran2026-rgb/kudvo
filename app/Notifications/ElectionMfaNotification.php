<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\OneTimePassword;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ElectionMfaNotification extends Notification
{
    public const VAR_CODE = '{#CODE#}';

    public const VAR_APP_DOMAIN = '{#APP_DOMAIN#}';

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
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->otp);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template): string
    {
        $variables = [
            static::VAR_CODE => $this->oneTimePassword->code,
            static::VAR_APP_DOMAIN => 'kudvo.com',
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }
}
