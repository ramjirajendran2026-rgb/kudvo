<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Resources\NominationResource;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

/**
 * @property Nomination $nomination
 */
abstract class NominationPage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = NominationResource::class;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord(key: $record);

        $this->authorizeAccess();
    }

    public function unsetProps(string|array $properties): void
    {
        foreach (Arr::wrap($properties) as $property) {
            unset($this->{$property});
        }
    }

    #[Computed]
    public function nomination(): Nomination
    {
        return Nomination::withCount(relations: ['positions', 'electors'])
            ->findOrFail($this->getRecord()->getKey());
    }

    public static function can(string $action, Nomination $nomination): bool
    {
        return NominationResource::can(action: $action, record: $nomination);
    }

    public static function cannot(string $action, Nomination $nomination): bool
    {
        return ! static::can(action: $action, nomination: $nomination);
    }

    public static function canAccess(Nomination $nomination): bool
    {
        return NominationResource::canView(record: $nomination);
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(
            boolean: static::canAccess(nomination: $this->nomination),
            code: 403,
        );
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    protected function canCancel(): bool
    {
        return ! $this->nomination->is_cancelled &&
            ! $this->nomination->is_scrutinised;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getEditAction()
                ->iconButton(),

            ActionGroup::make(actions: [
                $this->getCancelAction(),

            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    protected function getCancelAction(): Action
    {
        return Action::make(name: 'cancel')
            ->requiresConfirmation()
            ->color(color: 'warning')
            ->icon(icon: 'heroicon-s-archive-box-x-mark')
            ->label(label: 'Cancel')
            ->modalCancelActionLabel(label: 'No')
            ->modalSubmitActionLabel(label: 'Yes')
            ->successNotificationTitle(title: 'Cancelled')
            ->visible(condition: $this->canCancel())
            ->action(
                action: static function (Nomination $record, Action $action) {
                    $record->cancel();

                    $action->success();
                }
            );
    }

    protected function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form(form: fn (Form $form): Form => NominationResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->modalHeading(heading: fn (self $livewire) => 'Edit '.$livewire->getRecordTitle());
    }
}
