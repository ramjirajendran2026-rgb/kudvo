<?php

namespace App\Notifications\Nomination;

use App\Filament\Nomination\Resources\NomineeResource\Pages\ManageNominees;
use App\Models\Nomination;
use App\Models\Nominee;
use Filament\Notifications\Actions\Action;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Message;

class NomineeAcceptanceNotification extends Notification
{
    public function __construct(
        public Nominee $nominee
    ) {}

    public function via($notifiable): array
    {
        return [
            'database',
            'mail',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $nominee = $this->nominee;
        $position = $nominee->position;
        $proposer = $nominee->proposer;

        /** @var Nomination $nomination */
        $nomination = $position->event;

        return (new MailMessage)
            ->subject(subject: "Action Required: Nomination Proposal for $nomination->name")
            ->greeting(greeting: "Dear $nominee->display_name,")
            ->line(line: "You have been nominated for the position of $position->name by $proposer->display_name. Congratulations on this nomination!")
            ->line(line: 'To proceed further, we kindly request you to review the nomination and indicate your acceptance of the proposal. Your response is crucial for the progression of the nomination process.')
            ->line(line: 'Please click on the following button to access the nomination and respond accordingly.')
            ->action(text: 'Click Here', url: ManageNominees::getUrl(parameters: ['nomination' => $nomination]))
            ->line(line: 'If you have any questions or concerns, feel free to contact our support team.')
            ->line(line: 'Thank you for your prompt attention to this matter. We appreciate your active participation in the nomination process.')
            ->withSymfonyMessage(
                callback: fn (Message $message) => $message
                    ->getHeaders()
                    ->addTextHeader('Sensitivity', 'Private')
            );
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    public function toDatabase($notifiable)
    {
        $nominee = $this->nominee;
        $position = $nominee->position;
        $proposer = $nominee->proposer;

        /** @var Nomination $nomination */
        $nomination = $position->event;

        return \Filament\Notifications\Notification::make()
            ->title(title: "New Proposal for $position->name")
            ->body(body: "You have been nominated for the position of $position->name by $proposer->display_name")
            ->actions(actions: [
                Action::make(name: 'view')
                    ->markAsRead()
                    ->url(url: ManageNominees::getUrl(parameters: ['nomination' => $nomination])),
            ])
            ->getDatabaseMessage();
    }
}
