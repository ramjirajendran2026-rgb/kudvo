<?php

namespace App\Notifications\Election;

use App\Data\Election\BallotLinkNotificationData;
use App\Notifications\Concerns\HasSmsChannel;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BallotLinkNotification extends Notification
{
    use HasSmsChannel;

    public const VAR_BALLOT_LINK = '{#BALLOT_LINK#}';

    public const VAR_BALLOT_LINK_SHORT = '{#BALLOT_LINK_SHORT#}';

    public const VAR_ELECTION_NAME = '{#ELECTION_NAME#}';

    public const VAR_ELECTION_NAME_SHORT = '{#ELECTION_NAME_SHORT#}';

    public const VAR_ELECTOR_NAME = '{#ELECTOR_NAME#}';

    public const VAR_ELECTOR_NAME_SHORT = '{#ELECTOR_NAME_SHORT#}';

    public function __construct(
        protected BallotLinkNotificationData $data,
        protected array                      $via = [],
    ) { }

    public function via(object $notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: $this->via);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->data;

        return (new MailMessage)
            ->greeting(greeting: "Dear $data->electorName,")
            ->line(line: "Use the following link to cast your vote for $data->electionName.")
            ->action(text: "Vote Now", url: $data->ballotLink)
            ->line(line: 'Thank you for using our application!');
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->elector_ballot_link,
            notifiable: $notifiable
        );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_BALLOT_LINK => $this->data->ballotLink,
            static::VAR_BALLOT_LINK_SHORT => $this->data->ballotLinkShort,
            static::VAR_ELECTION_NAME => $this->data->electionName,
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->data->electionName, limit: 30),
            static::VAR_ELECTOR_NAME => $this->data->electorName,
            static::VAR_ELECTOR_NAME_SHORT => $this->data->electorName ?
                Str::maxLimit(value: $this->data->electorName, limit: 30) :
                null,
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }
}
