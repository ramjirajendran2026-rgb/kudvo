<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionDashboardState;
use App\Enums\ElectionStatus;
use App\Facades\Kudvo;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\ElectionResource;
use Cookie;
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
    public function clearCachedSubNavigation(): void
    {
        unset($this->cachedSubNavigation);
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
            static::can(action: 'setTiming', election: $election) => ElectionDashboardState::PendingTiming,
            static::can(action: 'publish', election: $election) => ElectionDashboardState::ReadyToPublish,
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
                ElectionResource\Widgets\ElectionVotingTrends::class,
                ElectionResource\Widgets\RecentlyVotedMembers::class,
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
                ElectionResource\Widgets\ElectionVotingTrends::class,
                ElectionResource\Widgets\RecentlyVotedMembers::class,
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
            ElectionDashboardState::PendingTiming => [ElectionResource::getSetTimingAction()],
            ElectionDashboardState::ReadyToPublish => [ElectionResource::getPublishAction()],
            ElectionDashboardState::Open,
            ElectionDashboardState::Expired => [ElectionResource::getCloseAction()],
            ElectionDashboardState::Closed => [ElectionResource::getGenerateResultAction()],
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

                ElectionResource::getCancelAction(),

                $this->getUseAsBoothDeviceAction(),

                $this->getRemoveFromBoothDeviceAction(),

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

    public function getUseAsBoothDeviceAction(): Action
    {
        return Action::make(name: 'useAsBoothDevice')
            ->requiresConfirmation()
            ->authorize(abilities: 'useAsBoothDevice')
            ->color(color: 'success')
            ->action(action: function (self $livewire, Action $action): void {
                Cookie::queue(Cookie::forever(name: 'election_booth_device', value: $livewire->getElection()->getKey()));

                $action->success();
            })
            ->successNotificationTitle(title: 'Enabled for booth voting.')
            ->visible(condition: fn (self $livewire): bool => Cookie::get(key: 'election_booth_device') != $livewire->getElection()->getKey());
    }

    public function getRemoveFromBoothDeviceAction(): Action
    {
        return Action::make(name: 'removeFromBoothDevice')
            ->requiresConfirmation()
            ->authorize(abilities: 'removeFromBoothDevice')
            ->color(color: 'danger')
            ->action(action: function (Action $action): void {
                Cookie::queue(Cookie::forget(name: 'election_booth_device'));

                $action->success();
            })
            ->successNotificationTitle(title: 'Disabled for booth voting.')
            ->visible(condition: fn (self $livewire): bool => Kudvo::isBoothDevice(election: $livewire->getElection()));
    }

    protected function hasPendingPreferenceSetup(): bool
    {
        return Preference::canAccessPage(election: $this->getElection()) &&
            ! Electors::canAccessPage(election: $this->getElection());
    }

    protected function hasPendingElectorSetup(): bool
    {
        return Electors::canAccessPage(election: $this->getElection()) &&
            ! BallotSetup::canAccessPage(election: $this->getElection());
    }

    protected function hasPendingBallotSetup(): bool
    {
        return BallotSetup::canAccessPage(election: $this->getElection()) &&
            ($this->getElection()->positions_count ?? $this->getElection()->loadCount(relations: ['positions'])->positions_count) < 1;
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
