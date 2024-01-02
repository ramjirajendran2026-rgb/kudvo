<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Resources\NominationResource;
use App\Filament\Resources\NominationResource\Pages\Concerns\InteractsWithState;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Form;

class Dashboard extends NominationPage
{
    use InteractsWithState;

    protected static string $view = 'filament.resources.nomination-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    protected function canSetupElectors(): bool
    {
        return ! $this->nomination->electors_count;
    }

    protected function canSetupPositions(): bool
    {
        return ! $this->nomination->positions_count;
    }

    protected function canPublish(): bool
    {
        return $this->canUpdateTiming() &&
            $this->nomination->electors_count &&
            $this->nomination->positions_count;
    }

    protected function canSetTiming(): bool
    {
        return $this->nomination->is_draft &&
            blank(value: $this->nomination->starts_at);
    }

    protected function canUpdateTiming(): bool
    {
        return $this->nomination->is_draft && filled(value: $this->nomination->starts_at);
    }

    public function getStateHeading(): ?string
    {
        return match (true) {
            $this->canPublish() => 'All set',
            $this->canSetTiming() => 'Just one step away',
            empty($this->nomination->positions_count) => 'Setup position',
            empty($this->nomination->electors_count) => 'Setup electors',
            default => null,
        };
    }

    protected function getStateActions(): array
    {
        return [
            $this->getElectorsPageAction(),

            $this->getPositionsPageAction(),

            $this->getTimingAction()
                ->visible(condition: $this->canSetTiming()),

            $this->getPublishAction(),
        ];
    }

    protected function getElectorsPageAction(): Action
    {
        return Action::make(name: 'electors_page')
            ->label(label: 'Manage electors')
            ->url(url: Electors::getUrl(parameters: [$this->nomination]))
            ->visible(condition: $this->canSetupElectors());
    }

    protected function getPositionsPageAction(): Action
    {
        return Action::make(name: 'positions_page')
            ->hidden(condition: $this->canSetupElectors())
            ->label(label: 'Manage positions')
            ->url(url: Positions::getUrl(parameters: [$this->nomination]))
            ->visible(condition: $this->canSetupPositions());
    }

    protected function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->color(color: 'success')
            ->icon(icon: 'heroicon-m-rocket-launch')
            ->modalIcon(icon: 'heroicon-o-rocket-launch')
            ->successNotificationTitle(title: 'Published')
            ->visible($this->canPublish());
    }

    protected function getTimingAction(): EditAction
    {
        return EditAction::make(name: 'edit_timing')
            ->form(form: fn (Form $form): Form => NominationResource::timingForm(form: $form))
            ->groupedIcon(icon: 'heroicon-m-clock')
            ->icon(icon: 'heroicon-m-clock')
            ->label(label: fn (self $livewire) => filled(value: $livewire->nomination->starts_at) ? 'Update Timing' : 'Set timing')
            ->modalHeading(heading: fn (self $livewire) => $livewire->getRecordTitle())
            ->mutateRecordDataUsing(callback: function (array $data): array {
                $data['timezone'] ??= Filament::getTenant()?->timezone;
                $data['starts_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 8);
                $data['ends_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 18);

                return $data;
            });
    }
}
