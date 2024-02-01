<?php

namespace App\Filament\Election\Pages;

use App\Facades\Kudvo;
use App\Forms\Components\VotePicker;
use App\Models\Ballot;
use App\Models\Position;
use App\Models\Vote;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class BallotPage extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.election.pages.ballot';

    protected static ?string $slug = 'ballot';

    public array $data = [];

    public bool $preview = false;

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
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => $livewire->preview),

                    Actions\Action::make(name: 'submit')
                        ->label(label: fn (self $livewire): string => $livewire->preview ? 'Confirm' : 'Continue')
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

            return;
        }

        if (
            $this->getElection()->preference->ip_restriction_threshold &&
            Ballot::query()
                ->whereIpAddress(value: request()->ip())
                ->whereHas(
                    relation: 'elector',
                    callback: fn (Builder $query): Builder => $query
                        ->whereMorphedTo(relation: 'event', model: $this->getElection())
                )
                ->count() >= $this->getElection()->preference->ip_restriction_threshold
        ) {
            Notification::make()
                ->title(title: 'Device not allowed')
                ->body(body: 'This device is already used by another member. Please use another device to cast your vote. It is advised to don\'t use shared Wi-Fi network for voting.')
                ->warning()
                ->send();

            return;
        }

        if (Cookie::has(key: 'election_'.Kudvo::getElection()->getKey().'_ballot')) {
            Notification::make()
                ->title(title: 'Device not allowed')
                ->body(body: 'This device is already used by another member. Please use another device to cast your vote.')
                ->warning()
                ->send();

            return;
        }

        $ballot = $this->getElector()->ballots()
            ->create(attributes: [
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

        Notification::make()
            ->title(title: 'Your votes confirmed successfully')
            ->success()
            ->send();

        Session::put(key: 'elector_'.$this->getElector()->getKey().'_votes', value: encrypt(value: $data));
        Cookie::queue(Cookie::forever(name: 'election_'.Kudvo::getElection()->getKey().'_ballot', value: $ballot->getKey()));

        $this->redirect(url: Filament::getUrl());
    }
}
