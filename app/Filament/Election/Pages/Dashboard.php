<?php

namespace App\Filament\Election\Pages;

use App\Enums\ElectionPanelDashboardState;
use App\Enums\ElectionPanelDashboardState as PanelState;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Pages\Ballot\Index;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Filament\ElectionPanel;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Models\Nominee;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property Collection<Nominee> $nominees
 */
class Dashboard extends BasePage
{
    use HasStateSection;

    protected static string $view = 'filament.election.pages.dashboard';

    protected $listeners = [
        'refresh' => 'reload'
    ];

    #[Locked]
    public ?array $sessionVotes = null;

    public function mount(Request $request): void
    {
        parent::mount(request: $request);

        if (Index::canAccess()) {
            $this->redirect(Index::getUrl());

            return;
        }

        if ($this->getElector()->ballot?->isVoted() && Session::has(key: 'elector_'.$this->getElector()->getKey().'_votes')) {
            $this->sessionVotes =  decrypt(value: Session::pull(key: 'elector_'.$this->getElector()->getKey().'_votes'));
        }
    }

    public static function canAccess(): bool
    {
        return static::can(action: 'viewBallotDashboard');
    }

    public function getState(): ?PanelState
    {
        return match (true) {
            $this->getElection()->is_upcoming => PanelState::YetToStart,
            $this->isVotedNow() => PanelState::VotedNow,
            $this->isAlreadyVoted() => PanelState::AlreadyVoted,
            $this->getElection()->is_closed => PanelState::Closed,
            $this->getElection()->is_completed => PanelState::Completed,
            $this->getElection()->is_expired => PanelState::Expired,
            default => null,
        };
    }

    public function getStateHeading(): string | Htmlable | null
    {
        return $this->getState()?->getLabel(election: $this->getElection());
    }

    public function getStateIcon(): ?string
    {
        return $this->getState()?->getIcon(election: $this->getElection());
    }

    public function getStateDescription(): string | Htmlable | null
    {
        return $this->getState()?->getDescription(election: $this->getElection());
    }

    protected function getStateActions(): array
    {
        $state = $this->getState();

        return match ($state) {
            PanelState::VotedNow => [
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

    public function hasSessionVotes(): bool
    {
        return filled($this->sessionVotes);
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

    public function reload(): void
    {
        $this->redirect(url: Filament::getUrl(), navigate: $this->isSpa());
    }
}
