<?php

namespace App\Notifications;

use App\Data\ElectionPreferenceData;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Concerns\HasSmsChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ElectorBallotLinkNotification extends Notification
{
    use HasSmsChannel;

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
        $preference = $this->getElection()->preference;

        return [
            ...Arr::wrap(value: $preference->ballot_link_mail ? 'mail' : null),
            ...Arr::wrap(value: $preference->ballot_link_sms ? $this->getSmsChannel(notifiable: $notifiable) : null),
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
        return $this->formatTemplate(template: app(abstract: SmsTemplates::class)->elector_ballot_link);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template): string
    {
        $variables = [
            static::VAR_BALLOT_LINK => url(path: '/'),
            static::VAR_BALLOT_LINK_SHORT => url(path: '/b/'.$this->getElector()->short_code),
            static::VAR_ELECTION_NAME => $this->getElection()->name,
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->getElection()->name, limit: 30),
            static::VAR_ELECTOR_NAME => $this->getElector()->display_name,
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

    protected function getPreference(): ElectionPreferenceData
    {
        return $this->getElection()->preference;
    }
}
