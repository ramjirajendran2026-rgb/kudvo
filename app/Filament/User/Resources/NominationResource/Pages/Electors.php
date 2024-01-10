<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\Forms\ElectorForm;
use App\Filament\Imports\ElectorImporter;
use App\Filament\User\Resources\ElectorResource;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Electors extends NominationPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.resources.nomination-resource.pages.electors';

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

    public function form(Form $form): Form
    {
        return ElectorResource::form(form: $form);
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
            ])
            ->recordTitleAttribute(attribute: 'membership_number');
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
            ->model(model: ElectorResource::getModel())
            ->modelLabel(label: ElectorResource::getModelLabel())
            ->form(form: fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->visible(condition: $this->canCreate());
    }

    protected function getEditAction(): TableEditAction
    {
        return ElectorResource::getTableEditAction()
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->iconButton()
            ->visible(condition: $this->canEdit());
    }

    protected function getDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton()
            ->visible(condition:$this->canDelete());
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return parent::canAccessPage($nomination) &&
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
