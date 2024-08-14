<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\User\Resources\ElectorResource;
use App\Models\Nomination;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Electors extends NominationPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.nomination-resource.pages.electors';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function getRelationshipName(): string
    {
        return 'electors';
    }

    public function getOwnerRecord(): Nomination
    {
        return $this->getNomination();
    }

    public function table(Table $table): Table
    {
        return ElectorResource::table(table: $table)
            ->actions(actions: [
                $this->getEditAction(),

                $this->getDeleteAction(),
            ])
            ->emptyStateActions(actions: [
                $this->getCreateAction(),
            ])
            ->headerActions(actions: [
                $this->getImportAction(),

                $this->getCreateAction(),
            ]);
    }

    protected function getImportAction(): TableImportAction
    {
        return ElectorResource::getTableImportAction()
            ->options(options: fn (self $livewire): array => [
                'event_type' => Nomination::class,
                'event_id' => $livewire->getNomination()->getKey(),
            ])
            ->visible(condition: $this->canImport());
    }

    protected function getCreateAction(): TableCreateAction
    {
        return ElectorResource::getTableCreateAction()
            ->visible(condition: $this->canCreate());
    }

    protected function getEditAction(): TableEditAction
    {
        return ElectorResource::getTableEditAction()
            ->visible(condition: $this->canEdit());
    }

    protected function getDeleteAction(): TableDeleteAction
    {
        return ElectorResource::getTableDeleteAction()
            ->visible(condition: $this->canDelete());
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return parent::canAccessPage(nomination: $nomination) &&
            static::can(action: 'viewAnyElector', nomination: $nomination);
    }

    protected function canCreate(): bool
    {
        return static::can(action: 'createElector', nomination: $this->getNomination());
    }

    protected function canImport(): bool
    {
        return static::can(action: 'importElector', nomination: $this->getNomination());
    }

    protected function canEdit(): bool
    {
        return static::can(action: 'updateAnyElector', nomination: $this->getNomination());
    }

    protected function canDelete(): bool
    {
        return static::can(action: 'deleteAnyElector', nomination: $this->getNomination());
    }
}
