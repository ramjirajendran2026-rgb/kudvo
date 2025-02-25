<?php

namespace App\Filament\Meeting\Pages;

use App\Enums\MeetingPanelState;
use App\Facades\Kudvo;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\Meeting\Pages\Concerns\BelongsToMeeting;
use App\Filament\Meeting\Pages\ResolutionVoting\Vote;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Form $form
 */
class Index extends Page
{
    use BelongsToMeeting;
    use HasStateSection;

    protected static string $view = 'filament.meeting.pages.index';

    protected static ?string $slug = '/';

    public function getHeading(): string | Htmlable
    {
        return $this->getMeeting()->name;
    }

    public function getParticipant(): Participant
    {
        /** @var Participant $participant */
        $participant = filament()->auth()->user();

        return $participant;
    }

    public function getPanelState(): ?MeetingPanelState
    {
        return Kudvo::getMeetingPanelState();
    }

    protected function getStateActions(): array
    {
        return match ($this->getPanelState()) {
            MeetingPanelState::AlreadyVoted => [
                Action::make('viewMyVotes')
                    ->icon('heroicon-m-eye')
                    ->url(Vote::getUrl()),
            ],
            MeetingPanelState::VotingOpen => [
                Action::make('proceedToVote')
                    ->icon('heroicon-m-arrow-right')
                    ->url(Vote::getUrl()),
            ],
            default => [],
        };
    }

    public function getStateDescription(): string | Htmlable | null
    {
        return $this->getPanelState()?->getDescription($this->getMeeting(), Filament::auth()->user());
    }

    public function getStateIcon(): ?string
    {
        return $this->getPanelState()?->getIcon($this->getMeeting(), Filament::auth()->user());
    }

    public function getStateHeading(): string | Htmlable | null
    {
        return $this->getPanelState()?->getHeading($this->getMeeting(), Filament::auth()->user());
    }
}
