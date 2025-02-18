<?php

namespace App\Filament\Meeting\Pages;

use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
use App\Forms\Components\ResolutionChoicePicker;
use App\Models\Resolution;
use App\Models\ResolutionVote;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Form $form
 */
class Resolutions extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.meeting.pages.resolutions';

    public ?array $data = [];

    public function mount(): void
    {
        parent::mount();

        $this->form->fill($this->getVotedChoices());
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
                        ->authorize(fn () => $this->canSubmit())
                        ->icon('heroicon-o-check')
                        ->size(ActionSize::ExtraLarge)
                        ->submit('form')
                        ->livewireTarget('submit'),
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

    public function getVotedChoices(): array
    {
        $participant = $this->getParticipant();

        return $participant->votes
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

        $this->js(
            <<<'JS'
Swal.fire({
    text: 'Your response has been submitted successfully.',
    title: 'Submitted',
    icon: 'success'
})
JS
        );
    }

    public function canSubmit(): bool
    {
        if (! $this->getMeeting()->isStatus(MeetingStatus::Published)) {
            return false;
        }

        if (! $this->getMeeting()->isVotingStatus(MeetingVotingStatus::Open)) {
            return false;
        }

        return ! $this->getParticipant()->is_voted;
    }
}
