<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Forms\PositionForm;
use App\Models\Nomination;
use App\Models\Position;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;

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

    public static function getNavigationLabel(): string
    {
        return 'Positions';
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
                        options: fn (self $livewire): array => $livewire
                            ->nomination
                            ->electors()
                            ->select(columns: ['groups'])
                            ->distinct()
                            ->pluck(column: 'groups')
                            ->flatten()
                            ->unique()
                            ->mapWithKeys(callback: fn (string $item): array => [$item => $item])
                            ->toArray()
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                $this->editAction(),

                $this->deleteAction(),
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
                    ->wrap(),
            ])
            ->defaultSort(column: 'sort')
            ->headerActions(actions: [
                $this->createAction(),
            ])
            ->reorderable(column: 'sort');
    }

    public function canReorder(): true
    {
        return true;
    }

    protected function createAction(): CreateAction
    {
        return CreateAction::make()
            ->createAnother(condition: false)
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->icon(icon: 'heroicon-m-plus')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->model(model: Position::class);
    }

    protected function editAction(): EditAction
    {
        return EditAction::make()
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium);
    }

    protected function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->iconButton();
    }
}
