<?php

namespace App\Notifications\Election;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Models\Election;
use App\Models\OneTimePassword;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponentFactory;
use App\Services\WhatsApp\Messages\WhatsAppMessage;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;
use App\Services\WhatsApp\WhatsAppChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Message;

class MfaCodeNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose
{
    use HasSmsChannel;

    public const VAR_CODE = '{#CODE#}';

    public const VAR_APP_DOMAIN = '{#APP_DOMAIN#}';

    public function __construct(
        protected Election $election,
        protected OneTimePassword $oneTimePassword
    ) {}

    public function via(object $notifiable): array
    {
        $preference = $this->election->preference;

        return [
            ...Arr::wrap(value: $preference->mfa_mail ? 'mail' : null),
            ...Arr::wrap(value: $preference->mfa_sms ? $this->getSmsChannel(notifiable: $notifiable) : null),
            ...Arr::wrap(value: $preference->mfa_whatsapp ? WhatsAppChannel::NAME : null),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $election = $this->getElection();
        $oneTimePassword = $this->getOneTimePassword();

        return (new MailMessage)
            ->subject(subject: "MFA Code for $election->name")
            ->line(line: "**$oneTimePassword->code** is your MFA code for $election->name")
            ->withSymfonyMessage(
                callback: fn (Message $message) => $message
                    ->getHeaders()
                    ->addTextHeader('Sensitivity', 'Private')
            );
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->elector_ballot_mfa);
    }

    public function toWhatsapp(object $notifiable): WhatsAppMessage
    {
        return WhatsAppMessageFactory::template('verification_code')
            ->addComponent(TemplateComponentFactory::body([
                TemplateComponentFactory::textParameter($this->getOneTimePassword()->code),
            ]))
            ->addComponent(TemplateComponentFactory::urlButton($this->getOneTimePassword()->code));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::BallotMfaCode;
    }

    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose
    {
        return SmsMessagePurpose::BallotMfaCode;
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

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getOneTimePassword(): OneTimePassword
    {
        return $this->oneTimePassword;
    }
}
