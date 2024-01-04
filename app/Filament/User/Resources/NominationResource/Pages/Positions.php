<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\Forms\PositionForm;
use App\Models\Nomination;
use App\Models\Position;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class Positions extends NominationPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.resources.nomination-resource.pages.positions';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $activeNavigationIcon = 'heroicon-s-briefcase';

    public static function getRelationshipName(): string
    {
        return 'positions';
    }

    public function getOwnerRecord(): Nomination
    {
        return $this->nomination;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                PositionForm::nameComponent(),

                PositionForm::quotaComponent()
                    ->inlineLabel(),

                PositionForm::abstainComponent()
                    ->live(),

                PositionForm::thresholdComponent()
                    ->inlineLabel(),

                PositionForm::groupsComponent()
                    ->options(
                        options: fn (self $livewire): array => Arr::mapWithKeys(
                            array: $livewire->nomination->getElectorGroups(),
                            callback: fn (string $item): array => [$item => $item]
                        )
                    )
                    ->visible(condition: filled(value: $this->nomination->getElectorGroups())),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                $this->getEditAction(),

                $this->getDeleteAction(),
            ])
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->label(label: 'Position name')
                    ->searchable()
                    ->wrap(),

                TextColumn::make(name: 'quota')
                    ->alignCenter()
                    ->label(label: 'Available posts')
                    ->numeric(),

                TextColumn::make(name: 'threshold')
                    ->alignCenter()
                    ->label(label: 'Min selection')
                    ->numeric(),

                TextColumn::make(name: 'elector_groups')
                    ->alignCenter()
                    ->badge()
                    ->label(label: 'Eligible groups')
                    ->visible(condition: filled(value: $this->nomination->getElectorGroups()))
                    ->wrap(),
            ])
            ->defaultSort(column: 'sort')
            ->headerActions(actions: [
                $this->getCreateAction(),
            ])
            ->reorderable(column: 'sort');
    }

    public static function canAccess(Nomination $nomination): bool
    {
        return parent::canAccess(nomination: $nomination) &&
            static::can(action: 'viewAnyPosition', nomination: $nomination);
    }

    protected function canCreate(): bool
    {
        return static::can(action: 'createPosition', nomination: $this->nomination);
    }

    protected function canReorder(): bool
    {
        return static::can(action: 'reorderPosition', nomination: $this->nomination);
    }

    protected function canEdit(): bool
    {
        return static::can(action: 'updateAnyPosition', nomination: $this->nomination);
    }

    protected function canDelete(): bool
    {
        return static::can(action: 'deleteAnyPosition', nomination: $this->nomination);
    }

    protected function getCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->createAnother(condition: false)
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->icon(icon: 'heroicon-m-plus')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->model(model: Position::class)
            ->visible(condition: $this->canCreate());
    }

    protected function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->visible(condition: $this->canEdit());
    }

    protected function getDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->iconButton()
            ->visible(condition: $this->canDelete());
    }
}
