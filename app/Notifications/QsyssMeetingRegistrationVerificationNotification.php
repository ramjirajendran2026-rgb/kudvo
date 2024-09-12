<?php

namespace App\Notifications;

use App\Notifications\Concerns\HasSmsChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QsyssMeetingRegistrationVerificationNotification extends Notification
{
    use HasSmsChannel;

    public const VAR_CODE = '{#CODE#}';

    public const VAR_APP_DOMAIN = '{#APP_DOMAIN#}';

    public function __construct(protected string $code) {}

    public function via($notifiable): array
    {
        return [
            $this->getSmsChannel(notifiable: $notifiable),
        ];
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->elector_ballot_mfa);
    }

    protected function formatTemplate(string $template): string
    {
        $variables = [
            static::VAR_CODE => $this->code,
            static::VAR_APP_DOMAIN => parse_url(url: url(path: '/'), component: PHP_URL_HOST),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }
}
