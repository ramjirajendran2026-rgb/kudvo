<?php

namespace App\Notifications\Nomination;

use App\Filament\Nomination\Resources\NomineeResource\Pages\ManageNominees;
use App\Models\Nomination;
use App\Models\Nominator;
use App\Models\Nominee;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SeconderAcceptanceNotification extends Notification
{
    public function __construct(
        public Nominee $nominee,
        public Nominator $seconder,
    )
    {
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
        $nominee = $this->nominee;
        $position = $nominee->position;
        $proposer = $nominee->proposer;
        $seconder = $this->seconder;

        /** @var Nomination $nomination */
        $nomination = $position->event;

        return (new MailMessage)
            ->subject(subject: "Action Required: Nomination Proposal for $nomination->name")
            ->greeting(greeting: "Dear $seconder->display_name,")
            ->line(
                line: "$nominee->display_name have been nominated for the position of $position->name by ".
                ($nominee->self_nomination ? "themselves" : $proposer->display_name).
                " and listed you as the one of the seconder for the nomination."
            )
            ->line(line: "To proceed further, we kindly request you to review the nomination and indicate your acceptance of the proposal. Your response is crucial for the progression of the nomination process.")
            ->line(line: "Please click on the following button to access the nomination and respond accordingly.")
            ->action(text: "Click Here", url: ManageNominees::getUrl(parameters: ['nomination' => $nomination]))
            ->line(line: "If you have any questions or concerns, feel free to contact our support team.")
            ->line(line: "Thank you for your prompt attention to this matter. We appreciate your active participation in the nomination process.");
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
            ->body(
                body: "$nominee->display_name have been nominated for the position of $position->name by ".
                ($nominee->self_nomination ? "themselves" : $proposer->display_name).
                " and listed you as the one of the seconder for the nomination."
            )
            ->getDatabaseMessage();
    }
}
