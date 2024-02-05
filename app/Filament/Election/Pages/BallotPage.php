<?php

namespace App\Filament\Election\Pages;

use App\Enums\BallotType;
use App\Facades\Kudvo;
use App\Forms\Components\VotePicker;
use App\Models\Position;
use App\Models\Vote;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;

class BallotPage extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.election.pages.ballot';

    protected static ?string $slug = 'ballot';

    public array $data = [];

    public bool $preview = false;

    public bool $flashVotes = false;

    public function mountCanAuthorizeAccess(): void
    {
        if (! static::canAccess()) {
            $this->redirect(url: Filament::getUrl());

            return;
        }

        $ballot = $this->getElector()->ballot;
        if (filled($ballot) && ! $this->getElection()->preference->voted_ballot_update) {
            $this->redirect(url: Filament::getUrl());

            return;
        }

        $this->form->fill(
            state: $ballot?->votes
                ->mapWithKeys(
                    callback: fn(Vote $vote): array => [
                        $vote->key => Arr::map($vote->secret, fn ($item) => $item['key'])
                    ]
                )
                ->toArray() ??
                []
        );
    }

    public static function canAccess(): bool
    {
        return static::can(action: 'vote');
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: fn (self $livewire): bool => $this->preview)
            ->model(model: $this->getElection())
            ->operation(operation: $this->preview ? 'preview' : 'create')
            ->statePath(path: 'data')
            ->schema(components: [
                Section::make()
                    ->compact()
                    ->visible(condition: fn (self $livewire): bool => $livewire->flashVotes)
                    ->schema(components: [
                        Placeholder::make(name: 'flashText')
                            ->content(content: 'Your votes are submitted as follows. This page will be automatically expire in 10 seconds.')
                            ->extraAttributes(attributes: [
                                'class' => 'text-success-600 dark:text-success-500 text-center'
                            ])
                            ->hiddenLabel(),
                    ]),

                ...$this->getElection()->positions
                    ->map(
                        callback: fn (Position $position) => VotePicker::makeFor(position: $position)
                            ->preview(condition: fn (self $livewire): bool => $livewire->preview),
                    )
                    ->toArray(),

                Actions::make(actions: [
                    Actions\Action::make(name: 'Back')
                        ->action(action: fn (self $livewire) => $livewire->preview = false)
                        ->color(color: 'gray')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes)
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => $livewire->preview),

                    Actions\Action::make(name: 'submit')
                        ->label(label: fn (self $livewire): string => $livewire->preview ? 'Confirm' : 'Continue')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes)
                        ->size(size: ActionSize::ExtraLarge)
                        ->submit(form: 'submit'),
                ])
                ->alignCenter(),
            ]);
    }

    public function submit(): void
    {
        if (! static::canAccess()) {
            $this->redirect(url: Filament::getUrl());

            return;
        }

        $data = $this->form->getState();

        if (! $this->preview) {
            $this->preview = true;

            $this->dispatch(event: 'scroll-to-top');
            return;
        }

        $ballot = $this->getElector()->ballots()
            ->create(attributes: [
                'type' => Kudvo::isBoothDevice() ? BallotType::Booth : BallotType::Direct,
                'ip_address' => request()->ip(),
                'voted_at' => now(),
                'auth_session_id' => $this->getElector()->authSession->getKey(),
            ]);

        foreach ($data as $key => $secret) {
            $vote = Vote::create(attributes: [
                'key' => $key,
                'secret' => $secret,
                'ballot_id' => $this->getElection()->preference->dnt_votes ? null : $ballot->getKey(),
            ]);
        }

        if (Kudvo::isBoothDevice()) {
            $this->flashVotes = true;

            $this->dispatch(event: 'scroll-to-top');
            $this->dispatch(event: 'flash-session-timeout');

            return;
        }

        Session::put(key: 'elector_'.$this->getElector()->getKey().'_votes', value: encrypt(value: $data));
        Cookie::queue(Cookie::forever(name: 'election_'.Kudvo::getElection()->getKey().'_ballot', value: $ballot->getKey()));

        $this->redirect(url: Filament::getUrl());
    }

    #[On(event: 'session-expired')]
    public function destroySession(): void
    {
        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(url: Filament::getUrl());
    }
}
