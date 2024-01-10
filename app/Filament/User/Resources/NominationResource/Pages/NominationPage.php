<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NominationStatus;
use App\Filament\User\Resources\NominationResource;
use App\Filament\User\Resources\NominationResource\Pages\Concerns\InteractsWithNomination;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;

abstract class NominationPage extends Page
{
    use InteractsWithNomination;

    protected static string $resource = NominationResource::class;

    public function mount(int|string $record): void
    {
        $this->nomination = $this->resolveNomination(key: $record);

        $this->authorizeAccess();
    }

    public static function can(string $action, Nomination $nomination): bool
    {
        return NominationResource::can(action: $action, record: $nomination);
    }

    public static function cannot(string $action, Nomination $nomination): bool
    {
        return ! static::can(action: $action, nomination: $nomination);
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return NominationResource::canView(record: $nomination);
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        if (! static::canAccessPage(nomination: $this->nomination)) {
            $this->redirect(Dashboard::getUrl(parameters: [$this->nomination]));
        }
    }

    protected function canEditTiming(): bool
    {
        return static::can(action: 'updateTiming', nomination: $this->nomination);
    }

    protected function canCancelNomination(): bool
    {
        return static::can(action: 'cancel', nomination: $this->nomination);
    }

    protected function canEditNomination(): bool
    {
        return static::can(action: 'update', nomination: $this->nomination);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getNominationEditAction()
                ->iconButton(),

            ActionGroup::make(actions: [
                $this->getTimingAction()
                    ->visible(condition: $this->canEditTiming()),

                $this->getNominationCancelAction(),

            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    protected function getNominationCancelAction(): Action
    {
        return Action::make(name: 'cancel')
            ->action(
                action: static function (Nomination $record, Action $action) {
                    $record->cancel();

                    $action->success();
                }
            )
            ->requiresConfirmation()
            ->color(color: NominationStatus::CANCELLED->getColor())
            ->icon(icon: NominationStatus::CANCELLED->getIcon())
            ->label(label: 'Cancel')
            ->modalCancelActionLabel(label: 'No')
            ->modalIcon(icon: NominationStatus::CANCELLED->getIcon())
            ->modalSubmitActionLabel(label: 'Yes')
            ->successNotificationTitle(title: 'Cancelled')
            ->visible(condition: $this->canCancelNomination());
    }

    protected function getNominationEditAction(): EditAction
    {
        return EditAction::make()
            ->form(form: fn (Form $form): Form => NominationResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->modalHeading(heading: fn (self $livewire) => 'Edit '.$livewire->getRecordTitle())
            ->visible(condition: $this->canEditNomination());
    }

    protected function getTimingAction(): EditAction
    {
        return EditAction::make(name: 'edit_timing')
            ->form(form: fn (Form $form): Form => NominationResource::timingForm(form: $form))
            ->groupedIcon(icon: 'heroicon-m-clock')
            ->icon(icon: 'heroicon-m-clock')
            ->label(label: 'Update Timing')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalHeading(heading: fn (self $livewire) => $livewire->getRecordTitle())
            ->modalWidth(width: MaxWidth::Medium)
            ->mutateRecordDataUsing(callback: function (array $data): array {
                $data['timezone'] ??= Filament::getTenant()?->timezone;
                $data['starts_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 8);
                $data['ends_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 18);

                return $data;
            });
    }
}
