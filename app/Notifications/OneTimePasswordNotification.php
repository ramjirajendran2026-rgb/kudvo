<?php

namespace App\Notifications;

use App\Models\OneTimePassword;
use App\Notifications\Concerns\HasSmsChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OneTimePasswordNotification extends Notification
{
    use HasSmsChannel;

    public const VAR_CODE = '{#CODE#}';

    public const VAR_APP_DOMAIN = '{#APP_DOMAIN#}';

    public function __construct(
        public OneTimePassword $oneTimePassword
    ) {}

    public function via($notifiable): array
    {
        return $this->prepareSmsChannel($notifiable, ['mail', 'sms']);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Your OTP is ' . $this->getOneTimePassword()->code);
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->elector_ballot_mfa);
    }

    protected function formatTemplate(string $template): string
    {
        $variables = [
            static::VAR_CODE => $this->oneTimePassword->code,
            static::VAR_APP_DOMAIN => parse_url(url: url(path: '/'), component: PHP_URL_HOST),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function getOneTimePassword(): OneTimePassword
    {
        return $this->oneTimePassword;
    }
}
