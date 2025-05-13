<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NominationStatus;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\Nomination\Resources\NomineeResource;
use App\Filament\User\Resources\NominationResource;
use App\Filament\User\Resources\NominationResource\Widgets\NominationStatsOverview;
use Filament\Actions\Action;
use Illuminate\Support\Js;

class Dashboard extends NominationPage
{
    use HasStateSection;

    protected static string $view = 'filament.user.resources.nomination-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    protected function getFooterWidgets(): array
    {
        return [
            NominationStatsOverview::class,
        ];
    }

    protected function getStateActions(): array
    {
        return [
            $this->getPreferencePageAction(),

            $this->getElectorsPageAction(),

            $this->getPositionsPageAction(),

            NominationResource::getEditTimingAction()
                ->name(name: 'set_timing')
                ->icon(icon: '')
                ->label(label: 'Set time')
                ->visible(condition: $this->canSetTiming()),

            $this->getPublishAction(),

            $this->getCloseAction(),

            Action::make('copyLink')
                ->alpineClickHandler(static fn (self $livewire) => sprintf(
                    <<<'JS'
navigator.clipboard.writeText(%s).then(() => new FilamentNotification().title('Copied successfully').success().send())
JS
                    ,
                    Js::encode(NomineeResource::getUrl(parameters: ['nomination' => $livewire->getNomination()], panel: 'nomination')),
                ))
                ->color('success')
                ->icon(icon: 'heroicon-m-clipboard-document')
                ->outlined()
                ->visible(condition: fn (self $livewire) => $livewire->canClose()),

            Action::make('shareLink')
                ->alpineClickHandler(fn (self $livewire) => sprintf(
                    <<<'JS'
navigator.share({
    title: %s,
    text: %s,
    url: %s
})
JS
                    ,
                    Js::encode($livewire->getNomination()->name),
                    Js::encode('Use this link to nominate a candidate.'),
                    Js::encode(NomineeResource::getUrl(parameters: ['nomination' => $livewire->getNomination()], panel: 'nomination')),
                ))
                ->icon('heroicon-m-share')
                ->outlined()
                ->visible(condition: fn (self $livewire) => $livewire->canClose()),

            Action::make('openInNewTab')
                ->icon(icon: 'heroicon-m-arrow-top-right-on-square')
                ->iconButton()
                ->url(
                    NomineeResource::getUrl(parameters: ['nomination' => $this->getNomination()], panel: 'nomination'),
                    shouldOpenInNewTab: true,
                )
                ->visible(condition: fn (self $livewire) => $livewire->canClose()),
        ];
    }

    protected function getPreferencePageAction(): Action
    {
        return Action::make(name: 'preference_page')
            ->label(label: 'Configure Preference')
            ->url(url: Preference::getUrl(parameters: [$this->getNomination()]))
            ->visible(condition: $this->hasPendingPreferenceSetup());
    }

    protected function getElectorsPageAction(): Action
    {
        return Action::make(name: 'electors_page')
            ->label(label: 'Continue setup')
            ->url(url: Electors::getUrl(parameters: [$this->getNomination()]))
            ->visible(condition: $this->hasPendingElectorSetup());
    }

    protected function getPositionsPageAction(): Action
    {
        return Action::make(name: 'positions_page')
            ->label(label: 'Continue setup')
            ->url(url: Positions::getUrl(parameters: [$this->getNomination()]))
            ->visible(condition: $this->hasPendingPositionSetup());
    }

    protected function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->color(color: NominationStatus::PUBLISHED->getColor())
            ->modalIcon(icon: NominationStatus::PUBLISHED->getIcon())
            ->successNotificationTitle(title: 'Published')
            ->visible(condition: $this->canPublish())
            ->action(action: function (Action $action): void {
                $this->getNomination()->publish();

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
                $this->getNomination()->close();

                $action->success();
            });
    }

    public function getStateDescription(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'Continue to configure nomination preferences',
            $this->hasPendingElectorSetup() => 'Bulk import or add manually',
            $this->hasPendingPositionSetup() => 'Add positions and available posts',
            $this->canSetTiming() => 'Set start time and end time for the nominations',
            $this->canPublish() => 'Once published, you are not allowed to modify any of elector and position information.',
            $this->canClose() => NomineeResource::getUrl(parameters: ['nomination' => $this->getNomination()], panel: 'nomination'),
            default => null,
        };
    }

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'Get started',
            $this->hasPendingElectorSetup() => 'Voters information',
            $this->hasPendingPositionSetup() => 'Configure positions',
            $this->canSetTiming() => 'Configure timing',
            $this->canPublish() => 'All set!',
            $this->canClose() => 'Nomination Published',
            default => null,
        };
    }

    protected function hasPendingPreferenceSetup(): bool
    {
        return Preference::canAccessPage(nomination: $this->getNomination()) &&
            ! Electors::canAccessPage(nomination: $this->getNomination());
    }

    protected function hasPendingElectorSetup(): bool
    {
        return Electors::canAccessPage(nomination: $this->getNomination()) &&
            ! Positions::canAccessPage(nomination: $this->getNomination());
    }

    protected function hasPendingPositionSetup(): bool
    {
        return Positions::canAccessPage(nomination: $this->getNomination()) &&
            ($this->getNomination()->positions_count ?? $this->getNomination()->loadCount(relations: ['positions'])->positions_count) < 1;
    }

    protected function canSetTiming(): bool
    {
        return static::can(action: 'setTiming', nomination: $this->getNomination());
    }

    protected function canPublish(): bool
    {
        return static::can(action: 'publish', nomination: $this->getNomination());
    }

    protected function canClose(): bool
    {
        return static::can(action: 'close', nomination: $this->getNomination());
    }

    public function getStateIcon(): ?string
    {
        return match (true) {
            $this->hasPendingPreferenceSetup() => 'heroicon-o-cog-6-tooth',
            $this->hasPendingElectorSetup() => 'heroicon-o-user-group',
            $this->hasPendingPositionSetup() => 'heroicon-o-briefcase',
            $this->canSetTiming() => 'heroicon-o-clock',
            $this->canPublish() => NominationStatus::PUBLISHED->getIcon(),
            default => null,
        };
    }

    protected function canUpdateTiming(): bool
    {
        return static::can(action: 'updateTiming', nomination: $this->getNomination());
    }
}
