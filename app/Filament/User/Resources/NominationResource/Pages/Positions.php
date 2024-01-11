<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\User\Resources\PositionResource;
use App\Models\Nomination;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

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
        return $this->getNomination();
    }

    public function form(Form $form): Form
    {
        return PositionResource::form(form: $form);
    }

    public function table(Table $table): Table
    {
        return PositionResource::table(table: $table)
            ->actions(actions: [
                $this->getEditAction(),

                $this->getDeleteAction(),
            ])
            ->emptyStateActions(actions: [
                $this->getCreateAction(),
            ])
            ->headerActions(actions: [
                $this->getCreateAction(),
            ]);
    }

    protected function getCreateAction(): CreateAction
    {
        return PositionResource::getTableCreateAction()
            ->visible(condition: $this->canCreate());
    }

    protected function getEditAction(): EditAction
    {
        return PositionResource::getTableEditAction()
            ->visible(condition: $this->canEdit());
    }

    protected function getDeleteAction(): TableDeleteAction
    {
        return PositionResource::getTableDeleteAction()
            ->visible(condition: $this->canDelete());
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return parent::canAccessPage(nomination: $nomination) &&
            static::can(action: 'viewAnyPosition', nomination: $nomination);
    }

    protected function canCreate(): bool
    {
        return static::can(action: 'createPosition', nomination: $this->getNomination());
    }

    protected function canReorder(): bool
    {
        return static::can(action: 'reorderPosition', nomination: $this->getNomination());
    }

    protected function canEdit(): bool
    {
        return static::can(action: 'updateAnyPosition', nomination: $this->getNomination());
    }

    protected function canDelete(): bool
    {
        return static::can(action: 'deleteAnyPosition', nomination: $this->getNomination());
    }
}
