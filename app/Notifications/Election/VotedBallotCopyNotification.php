<?php

namespace App\Notifications\Election;

use App\Models\Ballot;
use App\Models\Election;
use App\Models\Elector;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class VotedBallotCopyNotification extends Notification
{

    protected Election $election;

    protected Elector $elector;

    public function __construct(
        protected Ballot $ballot,
        protected string $votes,
        protected array $via = [],
    ) {
        $this->elector = $ballot->elector;
        $this->election = $this->elector->event;
    }

    public function via($notifiable): array
    {
        return $this->via;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(subject: 'Voted Ballot Copy - '.$this->election->name)
            ->greeting(greeting: 'Dear '.$this->elector->display_name.',')
            ->line(line: 'You have successfully cast your vote for '.$this->election->name.' on '.$this->ballot->voted_at->timezone(value: $this->election->timezone)->format(format: 'M j, Y h:i A (T)').'. Here is your ballot copy.')
            ->attachData(
                data: $this->generateBallotCopyPdf()->output(),
                name: 'ballot-copy-'.$this->election->code.'.pdf',
            );
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    protected function generateBallotCopyPdf(): Dompdf|\Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView(
            'pdf.election.ballot-copy',
            [
                'election' => $this->election,
                'elector' => $this->elector,
                'votes' => decrypt($this->votes),
            ],
            [],
            'UTF-8'
        );

        return $pdf
            ->setOption([
                'isRemoteEnabled' => true,
            ])
            ->setPaper(size: 'a4');
    }
}
