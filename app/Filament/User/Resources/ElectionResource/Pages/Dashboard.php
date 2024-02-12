<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionStatus;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\ElectionResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

class Dashboard extends ElectionPage
{
    use HasStateSection;

    protected static string $view = 'filament.user.resources.election-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'Get started',
            $this->hasPendingElectorSetup() => 'Voters information',
            $this->hasPendingBallotSetup() => 'Configure ballot',
            $this->canSetTiming() => 'Configure timing',
            $this->canPublish() => 'All set!',
            $this->canClose() => 'Election Published',
            $this->canGenerateResult() => 'Election Closed',
            default => null,
        };
    }

    public function getStateDescription(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'Continue to configure election preferences',
            $this->hasPendingElectorSetup() => 'Bulk import or add manually',
            $this->hasPendingBallotSetup() => 'Add positions and candidates',
            $this->canSetTiming() => 'Set start time and end time for the elections',
            $this->canPublish() => 'Once published, you are not allowed to modify any of elector and position information.',
            default => null,
        };
    }

    public function getStateIcon(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => Preference::getNavigationIcon(),
            $this->hasPendingElectorSetup() => Electors::getNavigationIcon(),
            $this->hasPendingBallotSetup() => BallotSetup::getNavigationIcon(),
            $this->canSetTiming() => 'heroicon-o-clock',
            $this->canPublish() => ElectionStatus::PUBLISHED->getIcon(),
            default => null,
        };
    }

    protected function getStateActions(): array
    {
        return [
            $this->getPreferencePageAction(),

            $this->getElectorsPageAction(),

            $this->getBallotPageAction(),

            ElectionResource::getSetTimingAction(),

            ElectionResource::getPublishAction(),

            ElectionResource::getCloseAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getPreviewBallotAction(),

            ActionGroup::make(actions: [
                ElectionResource::getEditAction(),

                ElectionResource::getEditTimingAction()
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
