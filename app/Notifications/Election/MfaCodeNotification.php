<?php

namespace App\Notifications\Election;

use App\Enums\SmsMessagePurpose;
use App\Models\Election;
use App\Models\OneTimePassword;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MfaCodeNotification extends Notification implements HasSmsMessagePurpose
{
    use HasSmsChannel;

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
        $preference = $this->election->preference;

        return [
            ...Arr::wrap(value: $preference->mfa_mail ? 'mail' : null),
            ...Arr::wrap(value: $preference->mfa_sms ? $this->getSmsChannel(notifiable: $notifiable) : null),
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
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->elector_ballot_mfa);
    }

    public function toArray(object $notifiable): array
    {
        return [];
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

    public function getSmsMessagePurpose(): SmsMessagePurpose
    {
        return SmsMessagePurpose::BallotMfaCode;
    }
}
