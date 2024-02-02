<?php

namespace App\Filament\Election\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\Contracts\HasElector;
use App\Filament\Election\Http\Middleware\EnsureDeviceIsAllowed;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Models\Ballot;
use App\Models\Nominee;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property Collection<Nominee> $nominees
 */
class Dashboard extends \Filament\Pages\Dashboard
{
    use HasStateSection;
    use InteractsWithElector;
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.dashboard';

    protected static string | array $withoutRouteMiddleware = EnsureDeviceIsAllowed::class;

    #[Locked]
    public ?array $sessionVotes = null;

    public function mount(): void
    {
        if (BallotPage::canAccess()) {
            $this->redirect(BallotPage::getUrl());

            return;
        }

        if ($this->getElector()->ballot?->isVoted() && Session::has(key: 'elector_'.$this->getElector()->getKey().'_votes')) {
            $this->sessionVotes =  decrypt(value: Session::pull(key: 'elector_'.$this->getElector()->getKey().'_votes'));
        }
    }

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->isVotedNow() => 'Voted successfully',
            $this->isAlreadyVoted() => 'Already voted',
            $this->getElection()->is_closed,
            $this->getElection()->is_completed => 'Voting closed',
            $this->getElection()->is_expired => 'Ballot expired',
            default => null,
        };
    }

    public function getStateIcon(): ?string
    {
        return match (true) {
            $this->isVotedNow(),
            $this->isAlreadyVoted() => 'heroicon-o-check-badge',
            $this->getElection()->is_closed,
            $this->getElection()->is_completed => 'heroicon-o-clock',
            $this->getElection()->is_expired => 'heroicon-o-clock',
            default => null,
        };
    }

    public function getStateDescription(): ?string
    {
        return match (true) {
            $this->isVotedNow() => 'You vote has been submitted successfully '.
                $this->getVotedAtLocal()?->format(format: 'M d, Y h:i A (T)'),
            $this->isAlreadyVoted() => 'You have already casted your vote on '.
                $this->getVotedAtLocal()?->format(format: 'M d, Y h:i A (T)'),
            $this->getElection()->is_closed,
            $this->getElection()->is_completed => 'Voting for this election has been closed on '.
                $this->getElection()->closed_at?->timezone(value: $this->getElection()->timezone)->format(format: 'M d, Y h:i A (T)'),
            $this->getElection()->is_expired => 'Voting ballot has been expired on '.
                $this->getElection()->ends_at_local->format(format: 'M d, Y h:i A (T)'),
            default => null,
        };
    }

    protected function getStateActions(): array
    {
        return match (true) {
            $this->isVotedNow() => [
                Action::make(name: 'downloadMyBallot')
                    ->action(action: 'downloadMyBallot')
                    ->visible(condition: fn (self $livewire): bool => $livewire->isVotedNow()),
            ],
            default => [],
        };
    }

    protected function isVotedNow(): bool
    {
        return filled(value: $this->sessionVotes);
    }

    protected function isAlreadyVoted(): bool
    {
        return (bool) $this->getElector()->ballot?->isVoted();
    }

    protected function getVotedAtLocal(): ?Carbon
    {
        return $this->getElector()->ballot?->voted_at?->timezone($this->getElection()->timezone);
    }

    protected function generateBallotCopyPdf(): Dompdf|\Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView(
            'pdf.election.ballot-copy',
            [
                'election' => $this->getElection(),
                'elector' => $this->getElector(),
                'votes' => $this->sessionVotes,
            ],
            [],
            'UTF-8'
        );

        $this->sessionVotes = null;

        return $pdf
            ->setOption([
                'isRemoteEnabled' => true,
            ])
            ->setPaper(size: 'a4');
    }

    public function downloadMyBallot(): StreamedResponse
    {
        return response()
            ->streamDownload(
                callback: function () {
                    echo $this->generateBallotCopyPdf()
                        ->output();
                },
                name: "ballot-{$this->getElection()->code}.pdf",
            );
    }
}
