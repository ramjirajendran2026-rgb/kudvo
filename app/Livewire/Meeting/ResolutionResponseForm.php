<?php

namespace App\Livewire\Meeting;

use App\Forms\Components\ResolutionChoicePicker;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\Resolution;
use App\Models\ResolutionVote;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\View\View;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ResolutionResponseForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    #[Locked]
    public bool $isPreview = false;

    #[Locked]
    public bool $isSubmitted = false;

    public ?Participant $participant = null;

    public Meeting $meeting;

    public function mount(): void
    {
        $this->form->fill($this->getVotedChoices());
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(fn () => $this->getParticipant()?->is_voted)
            ->schema([
                ...$this->getMeeting()->resolutions
                    ->map(fn (Resolution $resolution) => ResolutionChoicePicker::makeFor($resolution)),

                Actions::make([
                    $this->proceedAction(),
                ])->alignCenter(),
            ])
            ->statePath('data');
    }

    public function proceedAction()
    {
        return Action::make('proceed')
            ->action(fn (array $arguments) => $this->proceed($arguments))
            ->authorize(fn () => $this->canSubmit())
            ->icon('heroicon-o-arrow-right')
            ->label('Proceed to submit')
            ->size(ActionSize::ExtraLarge);
    }

    public function submitAction()
    {
        return \Filament\Actions\Action::make('submit')
            ->requiresConfirmation()
            ->action(fn () => $this->submit())
            ->authorize(fn () => $this->canSubmit())
            ->icon('heroicon-o-check')
            ->size(ActionSize::ExtraLarge);
    }

    public function proceed(array $arguments): void
    {
        $this->form->getState();

        $this->replaceMountedAction('submit', $arguments);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if ($this->isPreview || blank($participant = $this->getParticipant())) {
            $this->isSubmitted = true;

            return;
        }

        foreach ($data as $resolutionId => $choice) {
            $participant->votes()
                ->updateOrCreate(
                    ['resolution_id' => $resolutionId],
                    ['response' => $choice, 'weightage' => $participant->weightage],
                );
        }

        $participant->touch('voted_at');

        $this->isSubmitted = true;

        $this->dispatch('meeting-resolution-response-submitted', $this->getMeeting()->getKey(), $participant->getKey());
    }

    public function getMeeting(): Meeting
    {
        return $this->meeting;
    }

    public function getHeading()
    {
        return $this->getMeeting()->name;
    }

    public function getSubheading(): HtmlString
    {
        return new HtmlString(Markdown::parse(sprintf(
            '**%s** to **%s**',
            $this->getMeeting()->voting_starts_at_local->format('d M, Y h:i A (T)'),
            $this->getMeeting()->voting_ends_at_local->format('d M, Y h:i A (T)'),
        )));
    }

    public function getNotice()
    {
        return $this->getMeeting()->description;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function getVotedChoices(): array
    {
        return $this->getParticipant()?->votes
            ->mapWithKeys(fn (ResolutionVote $resolutionVote, int $key) => [$resolutionVote->resolution_id => $resolutionVote->response?->value])
            ->toArray() ?? [];
    }

    protected function canSubmit(): bool
    {
        if ($this->isPreview) {
            return true;
        }

        return Gate::allows('submitResolutionResponse', $this->getMeeting());
    }

    public function render(): View
    {
        return view('livewire.meeting.resolution-response-form');
    }
}
