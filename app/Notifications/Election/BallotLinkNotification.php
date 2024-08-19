<?php

namespace App\Notifications\Election;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Settings\SmsTemplates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BallotLinkNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose, ShouldQueue
{
    use HasSmsChannel;
    use Queueable;

    public const VAR_BALLOT_LINK = '{#BALLOT_LINK#}';

    public const VAR_BALLOT_LINK_SHORT = '{#BALLOT_LINK_SHORT#}';

    public const VAR_ELECTION_NAME = '{#ELECTION_NAME#}';

    public const VAR_ELECTION_NAME_SHORT = '{#ELECTION_NAME_SHORT#}';

    public const VAR_ELECTOR_NAME = '{#ELECTOR_NAME#}';

    public const VAR_ELECTOR_NAME_SHORT = '{#ELECTOR_NAME_SHORT#}';

    public function __construct(
        protected Elector $elector,
        protected Election $election,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: $this->getElection()->ballot_link_via);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $elector = $this->getElector();
        $election = $this->getElection();

        return (new MailMessage)
            ->subject(subject: "eVoting Link for $election->name - $elector->membership_number")
            ->greeting(greeting: "Dear $elector->display_name,")
            ->line(line: "Use the following link to cast your vote for $election->name.")
            ->action(text: 'Vote Now', url: $this->getBallotLink())
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

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return ! $this->getElector()->ballot?->isVoted();
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::BallotLink;
    }

    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose
    {
        return SmsMessagePurpose::BallotLink;
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_BALLOT_LINK => $this->getBallotLink(),
            static::VAR_BALLOT_LINK_SHORT => $this->getBallotLinkShort(),
            static::VAR_ELECTION_NAME => $this->getElection()->name,
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->getElection()->name, limit: 30),
            static::VAR_ELECTOR_NAME => $this->getElector()->display_name,
            static::VAR_ELECTOR_NAME_SHORT => $this->getElector()->display_name ?
                Str::maxLimit(value: $this->getElector()->display_name, limit: 30) :
                'Member',
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function getElector(): Elector
    {
        return $this->elector;
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getPreference()
    {
        return $this->getElection()->preference;
    }

    protected function getBallotLink(): string
    {
        return $this->getPreference()->ballot_link_unique ?
            URL::signedRoute(name: 'filament.election.eul', parameters: ['election' => $this->getElection(), 'elector' => $this->getElector()]) :
            route(name: 'filament.election.pages.index', parameters: ['election' => $this->getElection()]);
    }

    protected function getBallotLinkShort(): ?string
    {
        return $this->getPreference()->ballot_link_unique
            ? route(name: 'short_link.ballot', parameters: ['elector' => $this->getElector()->short_code])
            : route(name: 'short_link.election', parameters: ['election' => $this->getElection()->short_code]);
    }
}
