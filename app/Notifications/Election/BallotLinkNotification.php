<?php

namespace App\Notifications\Election;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Enums\WhatsAppMessagePurpose;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Notifications\Contracts\HasWhatsAppMessagePurpose;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponentFactory;
use App\Services\WhatsApp\Messages\WhatsAppMessage;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;
use App\Settings\SmsTemplates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Message;

class BallotLinkNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose, HasWhatsAppMessagePurpose, ShouldQueue
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
        $electionName = $this->getElectionName();

        return (new MailMessage)
            ->subject(subject: "eVoting Link for $electionName - $elector->membership_number")
            ->greeting(greeting: "Dear $elector->display_name,")
            ->line(line: "Use the following link to cast your vote for $election->name.")
            ->action(text: 'Vote Now', url: $this->getBallotLink())
            ->line(line: 'Thank you for using our application!')
            ->withSymfonyMessage(
                callback: fn (Message $message) => $message
                    ->getHeaders()
                    ->addTextHeader('Sensitivity', 'Private')
            );
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->elector_ballot_link,
            notifiable: $notifiable
        );
    }

    public function toWhatsapp(): WhatsAppMessage
    {
        return WhatsAppMessageFactory::template('ballot_invitation')
            ->addComponent(TemplateComponentFactory::header([
                TemplateComponentFactory::textParameter($this->getElectionName(), 'header'),
            ]))
            ->addComponent(TemplateComponentFactory::body([
                TemplateComponentFactory::textParameter($this->getElector()->display_name ?: $this->getElector()->membership_number, 'member_name'),
                TemplateComponentFactory::textParameter($this->getElection()->name, 'election_name'),
                TemplateComponentFactory::textParameter($this->getElection()->organisation->name, 'organization_name'),
                TemplateComponentFactory::textParameter($this->getElection()->starts_at_local->format('d M, Y h:i A (T)'), 'starts_at'),
                TemplateComponentFactory::textParameter($this->getElection()->ends_at_local->format('d M, Y h:i A (T)'), 'ends_at'),
            ]))
            ->addComponent(TemplateComponentFactory::urlButton(str($this->getBallotLinkShort(absolute: false))->replaceStart('/', '')->toString()));
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

    public function getWhatsAppMessagePurpose(object $notifiable): WhatsAppMessagePurpose
    {
        return WhatsAppMessagePurpose::BallotLink;
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_BALLOT_LINK => $this->getBallotLink(),
            static::VAR_BALLOT_LINK_SHORT => $this->getBallotLinkShort(),
            static::VAR_ELECTION_NAME => $this->getElectionName(),
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->getElectionName(), limit: 30),
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

    public function getElectionName(): ?string
    {
        return $this->getElection()->getShortName();
    }

    public function getPreference()
    {
        return $this->getElection()->preference;
    }

    protected function getBallotLink(bool $absolute = true): string
    {
        return $this->getPreference()->ballot_link_unique ?
            URL::signedRoute(name: 'filament.election.eul', parameters: ['election' => $this->getElection(), 'elector' => $this->getElector()], absolute: $absolute) :
            route(name: 'filament.election.pages.index', parameters: ['election' => $this->getElection()], absolute: $absolute);
    }

    protected function getBallotLinkShort(bool $absolute = true): ?string
    {
        return $this->getPreference()->ballot_link_unique
            ? route(name: 'short_link.go', parameters: ['b' => $this->getElector()->short_code], absolute: $absolute)
            : route(name: 'short_link.go', parameters: ['e' => $this->getElection()->short_code], absolute: $absolute);
    }
}
