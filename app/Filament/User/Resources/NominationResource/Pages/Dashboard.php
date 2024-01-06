<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NominationStatus;
use App\Filament\User\Resources\NominationResource\Pages\Concerns\InteractsWithState;
use Filament\Actions\Action;

class Dashboard extends NominationPage
{
    use InteractsWithState;

    protected static string $view = 'filament.resources.nomination-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    protected function hasPendingPreferenceSetup(): bool
    {
        return Preference::canAccess(nomination: $this->nomination) &&
            ! Electors::canAccess(nomination: $this->nomination);
    }

    protected function hasPendingElectorSetup(): bool
    {
        return Electors::canAccess(nomination: $this->nomination) &&
            ! Positions::canAccess(nomination: $this->nomination);
    }

    protected function hasPendingPositionSetup(): bool
    {
        return Positions::canAccess(nomination: $this->nomination) &&
            ($this->nomination->positions_count ?? $this->nomination->loadCount(relations: ['positions'])->positions_count) < 1;
    }

    protected function canPublish(): bool
    {
        return static::can(action: 'publish', nomination: $this->nomination);
    }

    protected function canClose(): bool
    {
        return static::can(action: 'close', nomination: $this->nomination);
    }

    protected function canSetTiming(): bool
    {
        return static::can(action: 'setTiming', nomination: $this->nomination);
    }

    protected function canUpdateTiming(): bool
    {
        return static::can(action: 'updateTiming', nomination: $this->nomination);
    }

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'No preference',
            $this->hasPendingElectorSetup() => 'No electors',
            $this->hasPendingPositionSetup() => 'No positions',
            $this->canSetTiming() => 'One step away',
            $this->canPublish() => 'All set!',
            $this->canClose() => 'Nomination Published',
            default => null,
        };
    }

    protected function getHeaderWidgets(): array
    {
        return [
//            NominationResource\Widgets\NominationStatsOverview::class,
        ];
    }

    protected function getStateActions(): array
    {
        return [
            $this->getPreferencePageAction(),

            $this->getElectorsPageAction(),

            $this->getPositionsPageAction(),

            $this->getTimingAction()
                ->name(name: 'set_timing')
                ->label(label: 'Set timing')
                ->visible(condition: $this->canSetTiming()),

            $this->getPublishAction(),

            $this->getCloseAction(),
        ];
    }

    protected function getPreferencePageAction(): Action
    {
        return Action::make(name: 'preference_page')
            ->icon(icon: 'heroicon-m-forward')
            ->label(label: 'Continue setup')
            ->url(url: Preference::getUrl(parameters: [$this->nomination]))
            ->visible(condition: $this->hasPendingPreferenceSetup());
    }

    protected function getElectorsPageAction(): Action
    {
        return Action::make(name: 'electors_page')
            ->icon(icon: 'heroicon-m-forward')
            ->label(label: 'Continue setup')
            ->url(url: Electors::getUrl(parameters: [$this->nomination]))
            ->visible(condition: $this->hasPendingElectorSetup());
    }

    protected function getPositionsPageAction(): Action
    {
        return Action::make(name: 'positions_page')
            ->icon(icon: 'heroicon-m-forward')
            ->label(label: 'Continue setup')
            ->url(url: Positions::getUrl(parameters: [$this->nomination]))
            ->visible(condition: $this->hasPendingPositionSetup());
    }

    protected function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->color(color: NominationStatus::PUBLISHED->getColor())
            ->icon(icon: NominationStatus::PUBLISHED->getIcon())
            ->modalIcon(icon: NominationStatus::PUBLISHED->getIcon())
            ->successNotificationTitle(title: 'Published')
            ->visible(condition: $this->canPublish())
            ->action(action: function (Action $action): void {
                $this->nomination->publish();

                $action->success();
            });
    }

    protected function getCloseAction(): Action
    {
        return Action::make(name: 'close')
            ->requiresConfirmation()
            ->color(color: NominationStatus::CLOSED->getColor())
            ->icon(icon: NominationStatus::CLOSED->getIcon())
            ->modalIcon(icon: NominationStatus::CLOSED->getIcon())
            ->successNotificationTitle(title: 'Closed')
            ->visible(condition: $this->canClose())
            ->action(action: function (Action $action): void {
                $this->nomination->close();

                $action->success();
            });
    }
}
