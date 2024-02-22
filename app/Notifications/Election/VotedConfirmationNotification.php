<?php

namespace App\Notifications\Election;

use App\Enums\SmsMessagePurpose;
use App\Models\Ballot;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class VotedConfirmationNotification extends Notification implements HasSmsMessagePurpose
{
    use HasSmsChannel;

    public const VAR_ELECTION_NAME = '{#ELECTION_NAME#}';

    public const VAR_ELECTION_NAME_SHORT = '{#ELECTION_NAME_SHORT#}';

    public const VAR_ELECTOR_NAME = '{#ELECTOR_NAME#}';

    public const VAR_ELECTOR_NAME_SHORT = '{#ELECTOR_NAME_SHORT#}';

    public const VAR_VOTED_AT = '{#VOTED_AT#}';

    protected Election $election;

    protected Elector $elector;

    public function __construct(
        protected Ballot $ballot,
        protected array $via = [],
    ) {
        $this->elector = $ballot->elector;
        $this->election = $this->elector->event;
    }

    public function via($notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: $this->via);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting(greeting: $this->formatTemplate(template: 'Dear '.static::VAR_ELECTOR_NAME.',', notifiable: $notifiable))
            ->line(line: $this->formatTemplate(template: 'You have successfully cast your vote for **'.static::VAR_ELECTION_NAME.'** on **'.static::VAR_VOTED_AT.'**.', notifiable: $notifiable));
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->elector_voted_confirmation,
            notifiable: $notifiable
        );
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_ELECTION_NAME => $this->election->name,
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->election->name, limit: 30),
            static::VAR_ELECTOR_NAME => $this->elector->display_name,
            static::VAR_ELECTOR_NAME_SHORT => Str::maxLimit(value: $this->elector->display_name, limit: 30),
            static::VAR_VOTED_AT => $this->ballot->voted_at->timezone(value: $this->election->timezone)->format(format: 'M j, Y h:i A'),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function getSmsMessagePurpose(): SmsMessagePurpose
    {
        return SmsMessagePurpose::VotedConfirmation;
    }
}
