<?php

namespace App\Filament\Meeting\Pages;

use App\Enums\MeetingPanelState;
use App\Facades\Kudvo;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\Meeting\Pages\Concerns\BelongsToMeeting;
use App\Forms\Components\ResolutionChoicePicker;
use App\Models\Participant;
use App\Models\Resolution;
use App\Models\ResolutionVote;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Form $form
 */
class Resolutions extends Page implements HasForms
{
    use BelongsToMeeting;
    use HasStateSection;
    use InteractsWithForms;

    protected static string $view = 'filament.meeting.pages.resolutions';

    public ?array $data = [];

    public function mount(): void
    {
        if ($this->canSubmit()) {
            $this->form->fill($this->getVotedChoices());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(fn () => $this->getParticipant()->is_voted)
            ->schema([
                ...$this->getMeeting()->resolutions
                    ->map(fn (Resolution $resolution) => ResolutionChoicePicker::makeFor($resolution)),

                Actions::make([
                    Action::make('submit')
                        ->requiresConfirmation()
                        ->action(fn () => $this->submit())
                        ->authorize(fn () => $this->canSubmit())
                        ->icon('heroicon-o-check')
                        ->size(ActionSize::ExtraLarge),
                ])->alignCenter(),
            ])
            ->statePath('data');
    }

    public function getHeading(): string | Htmlable
    {
        return $this->getMeeting()->name;
    }

    public function getNotice(): string | Htmlable | null
    {
        return $this->getMeeting()->description;
    }

    public function getParticipant(): Participant
    {
        /** @var Participant $participant */
        $participant = filament()->auth()->user();

        return $participant;
    }

    public function getVotedChoices(): array
    {
        return $this->getParticipant()->votes
            ->mapWithKeys(fn (ResolutionVote $resolutionVote, int $key) => [$resolutionVote->resolution_id => $resolutionVote->response?->value])
            ->toArray();
    }

    public function submit(): void
    {
        if (! $this->canSubmit()) {
            $this->js(
                <<<'JS'
Swal.fire({
    text: 'You are not allowed to vote. Please contact the meeting organizer for more information.',
    title: 'Not Allowed',
    icon: 'error'
})
JS
            );

            return;
        }

        $data = $this->form->getState();

        $participant = $this->getParticipant();

        foreach ($data as $resolutionId => $choice) {
            $participant->votes()
                ->updateOrCreate(
                    ['resolution_id' => $resolutionId],
                    ['response' => $choice, 'weightage' => $participant->weightage],
                );
        }

        $participant->touch('voted_at');

        $this->redirect(Filament::getCurrentPanel()->getUrl());
    }

    public function canSubmit(): bool
    {
        return $this->getPanelState() === MeetingPanelState::VotingOpen;
    }

    public function getPanelState(): ?MeetingPanelState
    {
        return Kudvo::getMeetingPanelState();
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
