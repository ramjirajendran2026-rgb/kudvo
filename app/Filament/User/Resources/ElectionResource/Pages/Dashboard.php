<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionDashboardState;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\ElectionResource;
use App\Models\ElectionPrice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;

class Dashboard extends ElectionPage
{
    use HasStateSection;

    protected static string $view = 'filament.user.resources.election-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public ElectionDashboardState $state;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->resolveState();
    }

    #[On('refresh')]
    public function resolveState(): void
    {
        $election = $this->getElection();

        $this->state = match (true) {
            $election->is_cancelled => ElectionDashboardState::Cancelled,
            $election->is_completed => ElectionDashboardState::Completed,
            $election->is_closed => ElectionDashboardState::Closed,
            $election->is_expired => ElectionDashboardState::Expired,
            $election->is_open => ElectionDashboardState::Open,
            $election->is_upcoming => ElectionDashboardState::Upcoming,
            static::can(action: 'publish', election: $election) &&
            $election->isCheckoutRequired() => ElectionDashboardState::PendingCheckout,
            static::can(action: 'publish', election: $election) => ElectionDashboardState::ReadyToPublish,
            static::can(action: 'setTiming', election: $election) => ElectionDashboardState::PendingTiming,
            BallotSetup::canAccessPage(election: $election) => ElectionDashboardState::PendingBallotSetup,
            Electors::canAccessPage(election: $election) => ElectionDashboardState::PendingElectorsList,
            Preference::canAccessPage(election: $election) => ElectionDashboardState::PendingPreference,
        };

        $this->cacheStateActions();

        unset($this->cachedSubNavigation);
    }

    public function getStateHeading(): ?string
    {
        return $this->state->getLabel(election: $this->getElection());
    }

    public function getStateDescription(): string | HtmlString | null
    {
        return $this->state->getDescription(election: $this->getElection());
    }

    public function getStateIcon(): ?string
    {
        return $this->state->getIcon(election: $this->getElection());
    }

    protected function getHeaderWidgets(): array
    {
        return match ($this->state) {
            ElectionDashboardState::Open,
            ElectionDashboardState::Expired =>[
                ElectionResource\Widgets\ElectionStatsOverview::class,
                ElectionResource\Widgets\VotedBallots::class,
            ],
            default => [],
        };
    }

    protected function getFooterWidgets(): array
    {
        return match ($this->state) {
            ElectionDashboardState::Closed,
            ElectionDashboardState::Completed =>[
                ElectionResource\Widgets\ElectionStatsOverview::class,
                ElectionResource\Widgets\VotedBallots::class,
            ],
            default => [],
        };
    }

    protected function getStateActions(): array
    {
        return match ($this->state) {
            ElectionDashboardState::PendingPreference => [$this->getPreferencePageAction()],
            ElectionDashboardState::PendingElectorsList => [$this->getElectorsPageAction()],
            ElectionDashboardState::PendingBallotSetup => [$this->getBallotPageAction()],
            ElectionDashboardState::PendingTiming => [
                ElectionResource::getSetTimingAction()
                    ->after(callback: fn (self $livewire) => $livewire->dispatch(event: 'refresh')),
            ],
            ElectionDashboardState::PendingCheckout => [$this->getProceedToPayAction()],
            ElectionDashboardState::ReadyToPublish => [
                ElectionResource::getPublishAction()
                    ->after(callback: fn (self $livewire) => $livewire->dispatch(event: 'refresh')),
            ],
            ElectionDashboardState::Open,
            ElectionDashboardState::Expired => [
                ElectionResource::getCloseAction()
                    ->after(callback: fn (self $livewire) => $livewire->dispatch(event: 'refresh')),
            ],
            ElectionDashboardState::Closed => [
                ElectionResource::getGenerateResultAction()
                    ->successRedirectUrl(url: fn (self $livewire) => static::getUrl(parameters: [$livewire->getElection()])),
            ],
            ElectionDashboardState::Completed => [$this->getResultPageAction()],
            default => [],
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getPreviewBallotAction(),

            ActionGroup::make(actions: [
                ElectionResource::getEditAction(),

                ElectionResource::getEditTimingAction()
                    ->after(callback: fn (self $livewire) => $livewire->resolveState())
                    ->modalHeading(heading: fn (self $livewire) => $livewire->getRecordTitle()),

                $this->getDownloadPhysicalBallotAction(),

                ElectionResource::getCancelAction(),

                Action::make(name: 'download_invoice')
                    ->url(url: fn (self $livewire) => $livewire->getElection()->stripe_invoice_data['invoice_pdf'])
                    ->visible(condition: fn (self $livewire) => filled($livewire->getElection()->stripe_invoice_data)),

            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    protected function getPreferencePageAction(): Action
    {
        return Action::make(name: 'preference_page')
            ->authorize(abilities: $this->hasPendingPreferenceSetup())
            ->label(label: 'Configure Preference')
            ->url(url: Preference::getUrl(parameters: [$this->getElection()]));
    }

    protected function getElectorsPageAction(): Action
    {
        return Action::make(name: 'electors_page')
            ->authorize(abilities: $this->hasPendingElectorSetup())
            ->label(label: 'Continue setup')
            ->url(url: Electors::getUrl(parameters: [$this->getElection()]));
    }

    protected function getBallotPageAction(): Action
    {
        return Action::make(name: 'ballot_page')
            ->authorize(abilities: $this->hasPendingBallotSetup())
            ->label(label: 'Continue setup')
            ->url(url: BallotSetup::getUrl(parameters: [$this->getElection()]));
    }

    protected function getResultPageAction(): Action
    {
        return Action::make(name: 'result_page')
            ->authorize(abilities: fn (self $livewire): bool => Result::canAccessPage(election: $livewire->getElection()))
            ->label(label: 'View Result')
            ->url(url: Result::getUrl(parameters: [$this->getElection()]));
    }

    protected function getProceedToPayAction(): Action
    {
        return Action::make(name: 'proceed_to_pay')
            ->action(action: function (self $livewire) {
                $election = $livewire->getElection();

                return $election->checkout(price: ElectionPrice::firstWhere('currency', 'USD'), user: auth()->user());
            })
            ->visible(condition: fn (self $livewire) => $livewire->getElection()->isCheckoutRequired())
            ->label(label: 'Proceed to Pay');
    }

    protected function getDownloadPhysicalBallotAction()
    {
        return Action::make(name: 'download_physical_ballot')
            ->action(action: function (self $livewire) {
                $election = $livewire->getElection();

                config(['app.name' => 'SecuredVoting']); // TODO: Remove this line after the issue is fixed

                $pdf = Pdf::loadView(
                    'pdf.election.physical-ballot',
                    [
                        'election' => $election,
                    ],
                    [],
                    'UTF-8'
                );

                return response()
                    ->streamDownload(
                        callback: function () use ($pdf) {
                            echo $pdf->output();
                        },
                        name: "physical-ballot-{$this->getElection()->code}.pdf",
                    );
            })
            ->authorize(abilities: 'downloadPhysicalBallot')
            ->hidden()
            ->label(label: 'Download Physical Ballot');
    }

    protected function hasPendingPreferenceSetup(): bool
    {
        return Preference::canAccessPage(election: $this->getElection()) &&
            $this->state == ElectionDashboardState::PendingPreference;
    }

    protected function hasPendingElectorSetup(): bool
    {
        return Electors::canAccessPage(election: $this->getElection()) &&
            $this->state == ElectionDashboardState::PendingElectorsList;
    }

    protected function hasPendingBallotSetup(): bool
    {
        return BallotSetup::canAccessPage(election: $this->getElection()) &&
            $this->state == ElectionDashboardState::PendingBallotSetup;
    }

    protected function canSetTiming(): bool
    {
        return static::can(action: 'setTiming', election: $this->getElection());
    }

    protected function canUpdateTiming(): bool
    {
        return static::can(action: 'updateTiming', election: $this->getElection());
    }

    protected function canPublish(): bool
    {
        return static::can(action: 'publish', election: $this->getElection());
    }

    protected function canClose(): bool
    {
        return static::can(action: 'close', election: $this->getElection());
    }

    protected function canGenerateResult(): bool
    {
        return static::can(action: 'generateResult', election: $this->getElection());
    }
}
