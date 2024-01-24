<?php

namespace App\Filament\Election\Pages;

use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Models\Nominee;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection;
use Jenssegers\Agent\Agent;

/**
 * @property Collection<Nominee> $nominees
 */
class Dashboard extends \Filament\Pages\Dashboard
{
    use HasStateSection;
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.dashboard';

    public function mount()
    {
        if (BallotPage::canAccess()) {
            $this->redirect(BallotPage::getUrl());
        }
    }

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->isAlreadyVoted() => 'Already voted',
            default => null,
        };
    }

    public function getStateIcon(): ?string
    {
        return match (true) {
            $this->isAlreadyVoted() => 'heroicon-o-check-badge',
            default => null,
        };
    }

    public function getStateDescription(): ?string
    {
        return match (true) {
            $this->isAlreadyVoted() => 'You have already casted your vote on '.$this->getElector()->ballot->voted_at->toFormattedDateString(),
            default => null,
        };
    }

    protected function isAlreadyVoted(): bool
    {
        return filled(value: $this->getElector()->ballot?->voted_at);
    }
}
