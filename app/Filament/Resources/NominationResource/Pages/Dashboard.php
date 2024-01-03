<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Enums\NominationStatusEnum;
use App\Filament\Resources\NominationResource;
use App\Filament\Resources\NominationResource\Pages\Concerns\InteractsWithState;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

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
            ->color(color: NominationStatusEnum::PUBLISHED->getColor())
            ->icon(icon: NominationStatusEnum::PUBLISHED->getIcon())
            ->modalIcon(icon: NominationStatusEnum::PUBLISHED->getIcon())
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
            ->color(color: NominationStatusEnum::CLOSED->getColor())
            ->icon(icon: NominationStatusEnum::CLOSED->getIcon())
            ->modalIcon(icon: NominationStatusEnum::CLOSED->getIcon())
            ->successNotificationTitle(title: 'Closed')
            ->visible(condition: $this->canClose())
            ->action(action: function (Action $action): void {
                $this->nomination->close();

                $action->success();
            });
    }
}
