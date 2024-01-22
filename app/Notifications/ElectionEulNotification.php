<?php

namespace App\Notifications;

use App\Models\Election;
use App\Models\ElectionPreference;
use App\Models\Elector;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ElectionEulNotification extends Notification
{
    public const VAR_BALLOT_LINK = '{#BALLOT_LINK#}';

    public const VAR_BALLOT_LINK_SHORT = '{#BALLOT_LINK_SHORT#}';

    public const VAR_ELECTION_NAME = '{#ELECTION_NAME#}';

    public const VAR_ELECTION_NAME_SHORT = '{#ELECTION_NAME_SHORT#}';

    public const VAR_ELECTOR_NAME = '{#ELECTOR_NAME#}';

    public const VAR_ELECTOR_NAME_SHORT = '{#ELECTOR_NAME_SHORT#}';

    public function __construct(
        protected Elector $elector,
    ) { }

    public function via(object $notifiable): array
    {
        $preference = $this->getPreference();

        return [
            ...$preference->eul_mail ? ['mail'] : [],
            ...$preference->eul_sms ? ['sms'] : [],
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->eul);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template): string
    {
        $variables = [
            static::VAR_BALLOT_LINK => url('/'),
            static::VAR_BALLOT_LINK_SHORT => url('/'),
            static::VAR_ELECTION_NAME => $this->getElection()->name,
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->getElector()->display_name, limit: 30),
            static::VAR_ELECTOR_NAME => $this->getElection()->name,
            static::VAR_ELECTOR_NAME_SHORT => Str::maxLimit(value: $this->getElector()->display_name, limit: 30),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    protected function getElector(): Elector
    {
        return $this->elector;
    }

    protected function getElection(): Election
    {
        return $this->getElector()->event;
    }

    protected function getPreference(): ElectionPreference
    {
        return $this->getElection()->preference;
    }
}
