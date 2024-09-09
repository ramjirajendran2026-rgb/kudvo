<?php

namespace App\Filament\Election\Pages\Ballot;

use App\Enums\BallotType;
use App\Events\ElectorCastedVoteInBoothEvent;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\BasePage;
use App\Forms\Components\VotesPicker;
use App\Models\Ballot;
use App\Models\Position;
use App\Models\Vote;
use App\Notifications\Election\VotedBallotCopyNotification;
use App\Notifications\Election\VotedConfirmationNotification;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;

class Index extends BasePage
{
    protected static string $view = 'filament.election.pages.ballot.index';

    protected static ?string $slug = 'ballot';

    public array $data = [];

    #[Locked]
    public bool $preview = false;

    #[Locked]
    public bool $flashVotes = false;

    #[Locked]
    public bool $isVoted = false;

    public function getTitle(): string | Htmlable
    {
        return 'Ballot - ' . $this->getElection()->name;
    }

    public function mountCanAuthorizeAccess(): void
    {
        if (! static::canAccess(mock: $this->isMock())) {
            $this->redirect(url: $this->getRedirectUrl(), navigate: $this->isSpa());

            return;
        }

        $this->form->fill(
            state: $this->getBallot()?->votes
                ->mapWithKeys(
                    callback: fn (Vote $vote): array => [
                        $vote->key => Arr::map($vote->secret?->toArray(), fn ($item) => $item['key']),
                    ]
                )
                ->toArray() ??
                []
        );
    }

    public static function canAccess(bool $mock = false): bool
    {
        return static::can(action: match (true) {
            $mock => 'mock',
            Kudvo::isBoothDevice() => 'boothVote',
            default => 'vote',
        });
    }

    public function form(Form $form): Form
    {
        $electorSegmentIds = $this->getElection()->preference->segmented_ballot ?
            $this->getElector()->segments()->pluck('id') :
            [];

        return $form
            ->disabled(condition: fn (self $livewire): bool => $this->preview)
            ->model(model: $this->getElection())
            ->operation(operation: $this->preview ? 'preview' : 'create')
            ->statePath(path: 'data')
            ->schema(components: [
                Placeholder::make(name: 'confirmation')
                    ->content(content: new HtmlString('<h2 class="text-lg md:text-xl font-semibold text-warning-600 dark:text-warning-400">Review & confirm your selection</h2>'))
                    ->extraAttributes(attributes: ['class' => 'text-center'])
                    ->hiddenLabel()
                    ->visible(condition: fn (self $livewire): bool => $this->preview && ! $this->isVoted),

                ...$this->getElection()->positions
                    ->when(
                        value: $this->getElection()->preference->segmented_ballot,
                        callback: fn (Collection $query) => $query
                            ->where(
                                fn (Position $position) => $position->segments()
                                    ->whereIn('id', $electorSegmentIds)
                                    ->exists(),
                            )
                    )
                    ->map(
                        callback: fn (Position $position) => VotesPicker::forPosition(
                            uuid: $position->uuid,
                            preference: $this->getElection()->preference
                        ),
                    )
                    ->toArray(),

                Actions::make(actions: [
                    $this->getBackAction(),

                    Actions\Action::make(name: 'continue')
                        ->label(label: __('filament.election.pages.ballot.index.form.actions.continue.label'))
                        ->action(action: 'submit')
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => ! $livewire->preview),

                    Actions\Action::make(name: 'confirm')
                        ->requiresConfirmation()
                        ->action(action: 'submit')
                        ->label(label: __('filament.election.pages.ballot.index.form.actions.confirm.label'))
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => $livewire->preview && ! $livewire->isVoted),

                    Actions\Action::make(name: 'print')
                        ->alpineClickHandler(handler: 'window.print()')
                        ->label(label: 'Print')
                        ->size(size: ActionSize::ExtraLarge)
                        ->visible(condition: fn (self $livewire): bool => $this->isVoted && $livewire->getElection()->booth_preference?->voted_ballot_print_by_self),
                ])
                    ->alignment(alignment: fn (self $livewire): Alignment => $livewire->preview ? Alignment::Between : Alignment::End)
                    ->extraAttributes(attributes: ['class' => 'px-2 md:px-0']),
            ]);
    }

    protected function getBackAction()
    {
        return Actions\Action::make(name: 'back')
            ->action(action: function (self $livewire) {
                $livewire->preview = false;

                $livewire->dispatch(event: 'scroll-to-top');
            })
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-chevron-left')
            ->label(label: __('filament.election.pages.ballot.index.form.actions.back.label'))
            ->size(size: ActionSize::ExtraLarge)
            ->visible(condition: fn (self $livewire): bool => $livewire->preview && ! $this->isVoted);
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

            $this->dispatch(event: 'scroll-to-top');

            return;
        }

        $ballot = $this->getElector()->ballots()
            ->updateOrCreate(
                attributes: [
                    'mock' => $this->isMock(),
                ],
                values: [
                    'type' => Kudvo::isBoothDevice() ? BallotType::Booth : BallotType::Direct,
                    'ip_address' => request()->ip(),
                    'voted_at' => now(),
                    'auth_session_id' => $this->getElector()->authSession->getKey(),
                    'booth_id' => Kudvo::getElectionBoothToken()?->getKey(),
                ]
            );

        if (! $ballot->wasRecentlyCreated) {
            $ballot->votes()->delete();
        }

        $voteIds = [];

        foreach ($data as $key => $secret) {
            $voteIds[] = Vote::create(attributes: [
                'key' => $key,
                'secret' => $secret,
                'mock' => $this->isMock(),
                'ballot_id' => $this->getElection()->preference->dnt_votes ? null : $ballot->getKey(),
                'booth_id' => Kudvo::getElectionBoothToken()?->getKey(),
            ])->getKey();
        }

        if (filled($acknowledgementVia = $this->getElection()->voted_confirmation_via)) {
            $this->getElector()->notify(
                new VotedConfirmationNotification(
                    ballot: $ballot,
                    via: $acknowledgementVia,
                )
            );
        }

        if (filled($ballotCopyVia = $this->getElection()->voted_ballot_copy_share_via)) {
            $this->getElector()->notify(
                new VotedBallotCopyNotification(
                    ballot: $ballot,
                    votes: encrypt(value: $data),
                    via: $ballotCopyVia,
                )
            );
        }

        $this->isVoted = true;
        Session::put(
            key: 'elector_' . $this->getElector()->getKey() . '_vote_ids' . ($this->isMock() ? '_mock' : ''),
            value: encrypt(value: $voteIds)
        );

        if (Kudvo::isBoothDevice()) {
            broadcast(event: new ElectorCastedVoteInBoothEvent(Kudvo::getElectionBoothToken()?->getKey()))
                ->toOthers();
        }

        if (false && Kudvo::isBoothDevice()) {
            $boothPreference = $this->getElection()->booth_preference;
            $this->flashVotes = $boothPreference->flash_voted_ballot;

            $interval = $boothPreference->after_vote_session_timeout;

            $this->dispatch(event: 'scroll-to-top');

            $this->dispatch(event: 'play-beep');

            if (filled($interval)) {
                $this->dispatch(event: 'flash-session-timeout', interval: $interval * 1000);

                Notification::make()
                    ->title(title: __('filament.election.pages.ballot.index.form.actions.submit.booth_success_notification.title'))
                    ->body(body: __('filament.election.pages.ballot.index.form.actions.submit.booth_success_notification.body', ['seconds' => $interval]))
                    ->success()
                    ->seconds(seconds: $interval)
                    ->send();
            }

            return;
        }

        Session::put(
            key: 'elector_' . $this->getElector()->getKey() . '_votes' . ($this->isMock() ? '_mock' : ''),
            value: encrypt(value: $data)
        );
        Cookie::queue(Cookie::forever(
            name: 'election_' . Kudvo::getElection()->getKey() . '_ballot' . ($this->isMock() ? '_mock' : ''),
            value: $ballot->getKey()
        ));

        $this->redirect(url: Filament::getUrl(), navigate: $this->isSpa());
    }

    protected function getBallot(): ?Ballot
    {
        if ($this->isMock()) {
            return $this->getElector()->mockBallot;
        }

        return $this->getElector()->ballot;
    }
}
