<?php

namespace App\Notifications\Nomination;

use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Nominee;
use App\Models\Position;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NominatorRequestNotification extends Notification
{
    protected Position $position;
    protected Nomination $nomination;

    public function __construct(
        protected Nominee $nominee,
    )
    {
        $this->position = $this->nominee->position;
        $this->nomination = $this->position->event;
    }

    public function via($notifiable): array
    {
        return [
            'database',
            'mail',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line(line: $this->getLine1())
            ->line(line: 'Use the below button to accept / decline.')
            ->action('Accept / Decline', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title(title: 'New nominator request')
            ->body(body: $this->getLine1())
            ->actions(actions: [
                Action::make(name: 'accept')
                    ->url(url: '/'),
            ])
            ->getDatabaseMessage();
    }

    protected function getLine1(): string
    {
        return 'Your co member '.
            ($this->nomination->self_nomination ? $this->nominee->display_name : $this->nominee->proposer?->display_name).
            ' nominated '.
            ($this->nomination->self_nomination ? 'himself' : $this->nominee->display_name).
            ' for the position of '.
            $this->position->name.
            ' to the upcoming '.
            $this->nomination->name.
            ' and mentioned you as the one of the nominator for this nomination.';
    }
}
