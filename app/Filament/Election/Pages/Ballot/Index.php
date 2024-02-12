<?php

namespace App\Filament\Election\Pages\Ballot;

use App\Enums\BallotType;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\BasePage;
use App\Forms\Components\VotePicker;
use App\Models\Ballot;
use App\Models\Position;
use App\Models\Vote;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class Index extends BasePage
{
    protected static string $view = 'filament.election.pages.ballot.index';

    protected static ?string $slug = 'ballot';

    public array $data = [];

    public bool $preview = false;

    public bool $flashVotes = false;

    public function mountCanAuthorizeAccess(): void
    {
        if (
            ! static::canAccess(mock: $this->isMock()) ||
            (
                filled($this->getBallot()) && ! $this->getElection()->preference->voted_ballot_update
            )
        ) {
            $this->redirect(url: $this->getRedirectUrl(), navigate: $this->isSpa());

            return;
        }

        $this->form->fill(
            state: $this->getBallot()?->votes
                ->mapWithKeys(
                    callback: fn(Vote $vote): array => [
                        $vote->key => Arr::map($vote->secret, fn ($item) => $item['key'])
                    ]
                )
                ->toArray() ??
                []
        );
    }

    public static function canAccess(bool $mock = false): bool
    {
        return static::can(action: $mock ? 'mockVote' : 'vote');
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: fn (self $livewire): bool => $this->preview)
            ->model(model: $this->getElection())
            ->operation(operation: $this->preview ? 'preview' : 'create')
            ->statePath(path: 'data')
            ->schema(components: [
                ...$this->getElection()->positions
                    ->map(
                        callback: fn (Position $position) => VotePicker::makeFor(position: $position)
                            ->photo(condition: $this->getElection()->preference->candidate_photo)
                            ->preview(condition: fn (self $livewire): bool => $livewire->preview)
                            ->symbol(condition: $this->getElection()->preference->candidate_symbol),
                    )
                    ->toArray(),

                Actions::make(actions: [
                    Actions\Action::make(name: 'Back')
                        ->action(action: function (self $livewire) {
                            $livewire->preview = false;

                            $livewire->dispatch(event: 'scroll-to-top');
                        })
                        ->color(color: 'gray')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes)
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => $livewire->preview),

                    Actions\Action::make(name: 'continue')
                        ->label(label: 'Continue')
                        ->action(action: 'submit')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes || $livewire->preview)
                        ->size(size: ActionSize::ExtraLarge)
                        ->submit(form: 'submit'),

                    Actions\Action::make(name: 'confirm')
                        ->requiresConfirmation()
                        ->action(action: 'submit')
                        ->label(label: 'Confirm')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes || ! $livewire->preview)
                        ->size(size: ActionSize::ExtraLarge),
                ])
                ->alignCenter(),
            ]);
    }

    public function submit(): void
    {
        if (! static::canAccess(mock: $this->isMock())) {
            $this->redirect(url: $this->getRedirectUrl(), navigate: $this->isSpa());

            return;
        }

        $data = $this->form->getState();

        if (! $this->preview) {
            $this->preview = true;

            Notification::make()
                ->title(title: 'Confirmation')
                ->body(body: 'Please review your selection and confirm')
                ->warning()
                ->send();

            $this->dispatch(event: 'scroll-to-top');
            return;
        }

        $ballot = $this->getElector()->ballots()
            ->create(attributes: [
                'type' => Kudvo::isBoothDevice() ? BallotType::Booth : BallotType::Direct,
                'ip_address' => request()->ip(),
                'voted_at' => now(),
                'mock' => $this->isMock(),
                'auth_session_id' => $this->getElector()->authSession->getKey(),
            ]);

        foreach ($data as $key => $secret) {
            $vote = Vote::create(attributes: [
                'key' => $key,
                'secret' => $secret,
                'mock' => $this->isMock(),
                'ballot_id' => $this->getElection()->preference->dnt_votes ? null : $ballot->getKey(),
            ]);
        }

        if (Kudvo::isBoothDevice()) {
            $this->flashVotes = true;

            $this->dispatch(event: 'scroll-to-top');
            $this->dispatch(event: 'flash-session-timeout');

            Notification::make()
                ->title(title: 'Submitted')
                ->body(body: 'Your votes are submitted successfully. This page will be automatically expire in 10 seconds.')
                ->success()
                ->send();

            return;
        }

        Session::put(
            key: 'elector_'.$this->getElector()->getKey().'_votes'.($this->isMock() ? '_mock': ''),
            value: encrypt(value: $data)
        );
        Cookie::queue(Cookie::forever(
            name: 'election_'.Kudvo::getElection()->getKey().'_ballots'.($this->isMock() ? '_mock': ''),
            value: array_merge(
                Cookie::get(
                    key: 'election_'.Kudvo::getElection()->getKey().'_ballots'.($this->isMock() ? '_mock': ''),
                    default: [],
                ),
                $ballot->getKey(),
            )
        ));

        $this->redirect(url: Filament::getUrl());
    }

    protected function getBallot(): ?Ballot
    {
        if ($this->isMock()) {
            return $this->getElector()->mockBallot;
        }

        return $this->getElector()->ballot;
    }

    #[On(event: 'session-expired')]
    public function destroySession(): void
    {
        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(url: $this->getRedirectUrl(), navigate: $this->isSpa());
    }
}
