<?php

namespace App\Filament\Election\Pages;

use App\Enums\ElectionPanelState;
use App\Events\Election\Booth\PrintBallot;
use App\Events\ElectorRevokedFromBoothEvent;
use App\Facades\Kudvo;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Forms\Components\VotesPicker;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Position;
use App\Models\Vote;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Exists;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

/**
 * @property Form $form
 */
class Index extends Page
{
    use HasStateSection;
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.index';

    protected static ?string $slug = '/';

    public ?ElectionPanelState $state = null;

    #[Locked]
    public ?string $sessionVotes = null;

    #[Locked]
    public ?string $sessionVoteIds = null;

    #[Locked]
    public bool $isVotedNow = true;

    public bool $playVoteCastedMessage = false;

    public bool $autoPrint = false;

    public bool $mock;

    public ?array $data = [];

    public function getListeners(): array
    {
        $listeners = parent::getListeners();

        if (Kudvo::isBoothDevice()) {
            $boothId = Kudvo::getElectionBoothToken()->getKey();

            $listeners["echo:election-booth.$boothId,." . ElectorRevokedFromBoothEvent::getBroadcastName()] = 'destroySession';
            $listeners["echo:election-booth.$boothId,." . PrintBallot::getBroadcastName()] = 'dispatchPrintBallotEvent';
        }

        return $listeners;
    }

    public function mount(Request $request): void
    {
        $this->mock = $request->query(key: 'mock', default: false);

        $this->state = Kudvo::getElectionPanelState();

        if ($this->state == ElectionPanelState::Open) {
            $this->redirect(url: Ballot\Index::getUrl(), navigate: $this->isSpa());

            return;
        }

        $this->sessionVotes = Session::pull(key: 'elector_' . ($this->getElector()?->getKey()) . '_votes');
        if (filled($this->sessionVotes)) {
            $this->playVoteCastedMessage = true;
        }

        $this->sessionVoteIds = Session::get(key: 'elector_' . ($this->getElector()?->getKey()) . '_vote_ids');

        if (filled($this->sessionVoteIds)) {
            $this->form->fill(
                state: Vote::find(decrypt($this->sessionVoteIds))
                    ->mapWithKeys(
                        callback: fn (Vote $vote): array => [
                            $vote->key => Arr::map($vote->secret?->toArray(), fn ($item) => $item['key']),
                        ]
                    )
                    ->toArray() ??
                []
            );

            $this->autoPrint = $this->canAutoPrintBallot();
        }
    }

    protected function canSelfPrintBallot(): bool
    {
        return $this->canPrintBallot() &&
            Kudvo::getElection()->booth_preference?->voted_ballot_print_by_self;
    }

    protected function canAutoPrintBallot(): bool
    {
        return $this->canPrintBallot() &&
            filled($this->sessionVotes) &&
            Kudvo::getElection()->booth_preference?->voted_ballot_auto_print;
    }

    protected function canPrintBallot(): bool
    {
        return filled($this->sessionVoteIds) &&
            Kudvo::isBoothDevice();
    }

    public static function getRelativeRouteName(): string
    {
        return 'index';
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return [
            EnsureStateIsAllowed::class,
            ...$panel->getAuthMiddleware(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getElection()->name;
    }

    public function isSpa(): bool
    {
        return Filament::getCurrentPanel()->hasSpaMode();
    }

    public function isMock(): bool
    {
        return $this->mock;
    }

    public function getState(): ?ElectionPanelState
    {
        return $this->state;
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
        return $this->getState()?->getDescription(election: $this->getElection(), elector: $this->getElector());
    }

    protected function getStateActions(): array
    {
        return match ($this->getState()) {
            ElectionPanelState::CommonLinkRestricted => [
                Action::make('request_voting_link')
                    ->requiresConfirmation()
                    ->modalDescription('Voting link will be sent to only non-voted members only. Are you sure you would like to do this?')
                    ->action(function (array $data) {
                        $elector = $this->getElection()
                            ->electors()
                            ->firstWhere('membership_number', $data['membership_number']);

                        if (! $elector->ballot?->isVoted()) {
                            $elector->sendBallotLink();
                        }

                        $this->js(
                            <<<'JS'
Swal.fire({
    title: 'Request submitted',
    text: 'An unique voting link has been sent to your registered Email address / Phone number.',
    icon: 'success'
})
JS
                        );
                    })
                    ->form([
                        TextInput::make('membership_number')
                            ->exists(
                                'electors',
                                'membership_number',
                                fn (Exists $rule) => $rule->where('event_type', Election::class)
                                    ->where('event_id', $this->getElection()->getKey())
                            )
                            ->placeholder('Enter your membership number')
                            ->required(),
                    ]),
            ],
            ElectionPanelState::Voted => [
                Action::make(name: 'downloadMyBallot')
                    ->action(
                        action: fn (self $livewire) => response()
                            ->streamDownload(
                                callback: function () use ($livewire) {
                                    echo $livewire->generateBallotCopyPdf()
                                        ->output();
                                },
                                name: "ballot-{$livewire->getElection()->code}.pdf",
                            )
                    )
                    ->visible(
                        condition: fn (self $livewire): bool => ! Kudvo::isBoothDevice() &&
                            filled($livewire->sessionVotes) &&
                            $livewire->getElection()->preference->voted_ballot_download
                    ),

                Action::make(name: 'editMyVotes')
                    ->color(color: 'info')
                    ->url(url: Ballot\Index::getUrl())
                    ->visible(condition: fn () => Ballot\Index::canAccess()),

                Action::make(name: 'printMyBallot')
                    ->alpineClickHandler(handler: 'window.print()')
                    ->label(label: 'Print My Ballot')
                    ->visible(condition: fn (self $livewire): bool => $livewire->canSelfPrintBallot()),
            ],
            default => [],
        };
    }

    public function form(Form $form): Form
    {
        $electorSegmentIds = $this->getElection()->preference->segmented_ballot ?
            $this->getElector()->segments()->pluck('id') :
            [];

        $boothPreference = $this->getElection()->booth_preference;

        return $form
            ->extraAttributes(attributes: [
                'class' => Arr::toCssClasses(
                    array: collect()
                        ->when(
                            value: Kudvo::isBoothDevice(),
                            callback: fn (Collection $collection) => $collection
                                ->when(
                                    value: $boothPreference?->flash_voted_ballot,
                                    callback: fn (Collection $collection) => $collection,
                                    default: fn (Collection $collection) => $collection->add(item: 'hidden'),
                                )
                                ->when(
                                    value: $boothPreference?->voted_ballot_print_by_self || $boothPreference?->voted_ballot_print_by_admin,
                                    callback: fn (Collection $collection) => $collection->add(item: 'print:grid'),
                                ),
                            default: fn (Collection $collection) => $collection->add(item: 'hidden')
                        )
                        ->toArray()
                ),
            ])
            ->disabled()
            ->model(model: $this->getElection())
            ->operation(operation: 'preview')
            ->statePath(path: 'data')
            ->schema(components: [
                ...$this->getElection()->positions
                    ->when(
                        value: $this->getElection()->preference->segmented_ballot,
                        callback: fn (EloquentCollection $query) => $query
                            ->where(
                                fn (Position $position) => $position->segments()
                                    ->whereIn('id', $electorSegmentIds)
                                    ->exists(),
                            )
                    )
                    ->map(
                        callback: fn (Position $position) => VotesPicker::forPosition(
                            uuid: $position->uuid,
                            preference: $this->getElection()->preference,
                        ),
                    )
                    ->toArray(),
            ]);
    }

    protected function getElector(): ?Elector
    {
        /** @var ?Elector $elector */
        $elector = Filament::auth()->user();

        return $elector;
    }

    protected function generateBallotCopyPdf(): Dompdf | \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView(
            'pdf.election.ballot-copy',
            [
                'election' => $this->getElection(),
                'elector' => $this->getElector(),
                'votes' => decrypt(value: $this->sessionVotes),
            ],
            [],
            'UTF-8'
        );

        $this->sessionVotes = null;

        return $pdf
            ->setOption([
                'isRemoteEnabled' => true,
            ])
            ->setPaper('a4');
    }

    #[On(event: 'session-expired')]
    public function destroySession()
    {
        Filament::auth()->logout();

        return app(LogoutResponse::class);
    }

    public function dispatchPrintBallotEvent(): void
    {
        $this->dispatch('print-ballot');
    }
}
