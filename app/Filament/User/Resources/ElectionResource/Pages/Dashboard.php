<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionStatus;
use App\Filament\User\Contracts\HasElection;
use App\Filament\User\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\ElectionResource;
use App\Filament\User\Resources\NominationResource\Pages\Positions;
use App\Models\Election;
use Filament\Actions\Action;

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
            $this->hasPendingPositionSetup() => 'Configure positions',
            $this->canSetTiming() => 'Configure timing',
            $this->canPublish() => 'All set!',
            $this->canClose() => 'Election Published',
            default => null,
        };
    }

    public function getStateDescription(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'Continue to configure election preferences',
            $this->hasPendingElectorSetup() => 'Bulk import or add manually',
            $this->hasPendingPositionSetup() => 'Add positions and available posts',
            $this->canSetTiming() => 'Set start time and end time for the elections',
            $this->canPublish() => 'Once published, you are not allowed to modify any of elector and position information.',
            default => null,
        };
    }

    public function getStateIcon(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'heroicon-o-cog-6-tooth',
            $this->hasPendingElectorSetup() => 'heroicon-o-user-group',
            $this->hasPendingPositionSetup() => 'heroicon-o-briefcase',
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

            $this->getPositionsPageAction(),

            ElectionResource::getEditTimingAction()
                ->name(name: 'set_timing')
                ->icon(icon: '')
                ->label(label: 'Set time')
                ->record(record: fn (HasElection $livewire): Election => $livewire->getElection())
                ->recordTitle('name')
                ->visible(condition: $this->canSetTiming()),

            $this->getPublishAction(),

            $this->getCloseAction(),
        ];
    }

    protected function getPreferencePageAction(): Action
    {
        return Action::make(name: 'preference_page')
            ->label(label: 'Configure Preference')
            ->url(url: Preference::getUrl(parameters: [$this->getElection()]))
            ->visible(condition: $this->hasPendingPreferenceSetup());
    }

    protected function getElectorsPageAction(): Action
    {
        return Action::make(name: 'electors_page')
            ->label(label: 'Continue setup')
            ->url(url: Electors::getUrl(parameters: [$this->getElection()]))
            ->visible(condition: $this->hasPendingElectorSetup());
    }

    protected function getPositionsPageAction(): Action
    {
        return Action::make(name: 'positions_page')
            ->label(label: 'Continue setup')
            ->url(url: Positions::getUrl(parameters: [$this->getElection()]))
            ->visible(condition: $this->hasPendingPositionSetup());
    }

    protected function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->color(color: ElectionStatus::PUBLISHED->getColor())
            ->modalIcon(icon: ElectionStatus::PUBLISHED->getIcon())
            ->successNotificationTitle(title: 'Published')
            ->visible(condition: $this->canPublish())
            ->action(action: function (Action $action): void {
                $this->getElection()->publish();

                $action->success();
            });
    }

    protected function getCloseAction(): Action
    {
        return Action::make(name: 'close')
            ->requiresConfirmation()
            ->color(color: ElectionStatus::CLOSED->getColor())
            ->icon(icon: ElectionStatus::CLOSED->getIcon())
            ->modalIcon(icon: ElectionStatus::CLOSED->getIcon())
            ->successNotificationTitle(title: 'Closed')
            ->visible(condition: $this->canClose())
            ->action(action: function (Action $action): void {
                $this->getElection()->close();

                $action->success();
            });
    }

    protected function hasPendingPreferenceSetup(): bool
    {
        return Preference::canAccessPage(election: $this->getElection()) &&
            ! Electors::canAccessPage(election: $this->getElection());
    }

    protected function hasPendingElectorSetup(): bool
    {
        return Electors::canAccessPage(election: $this->getElection()) &&
            ! Ballot::canAccessPage(election: $this->getElection());
    }

    protected function hasPendingPositionSetup(): bool
    {
        return Ballot::canAccessPage(election: $this->getElection()) &&
            ($this->getElection()->positions_count ?? $this->getElection()->loadCount(relations: ['positions'])->positions_count) < 1;
    }

    protected function canPublish(): bool
    {
        return static::can(action: 'publish', election: $this->getElection());
    }

    protected function canClose(): bool
    {
        return static::can(action: 'close', election: $this->getElection());
    }

    protected function canSetTiming(): bool
    {
        return static::can(action: 'setTiming', election: $this->getElection());
    }

    protected function canUpdateTiming(): bool
    {
        return static::can(action: 'updateTiming', election: $this->getElection());
    }
}
