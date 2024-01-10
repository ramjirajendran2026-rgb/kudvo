<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\Forms\ElectorForm;
use App\Filament\Imports\ElectorImporter;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
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
        return $form
            ->schema(components: [
                ElectorForm::membershipNumberComponent(),

                Group::make()
                    ->columns()
                    ->schema(components: [
                        ElectorForm::firstNameComponent(),

                        ElectorForm::lastNameComponent(),
                    ]),

                ElectorForm::emailComponent(),

                ElectorForm::phoneComponent(),

                ElectorForm::groupsComponent(),
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
                TextColumn::make(name: 'membership_number')
                    ->badge()
                    ->label(label: 'Membership number')
                    ->searchable(),

                TextColumn::make(name: 'full_name')
                    ->label(label: 'Full name')
                    ->wrap(),

                TextColumn::make(name: 'phone')
                    ->label(label: 'Phone number'),

                TextColumn::make(name: 'email')
                    ->label(label: 'Email address')
                    ->wrap(),

                TextColumn::make(name: 'groups')
                    ->badge()
                    ->separator()
                    ->wrap(),
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

    protected function getCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->createAnother(condition: false)
            ->form(static fn (self $livewire, Form $form): Form => $livewire->form($form))
            ->icon(icon: 'heroicon-m-plus')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->model(model: Elector::class)
            ->visible(condition: $this->canCreate());
    }

    protected function getImportAction(): ImportAction
    {
        return ImportAction::make()
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: ElectorImporter::class)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->options(options: fn (self $livewire): array => [
                'event_type' => Nomination::class,
                'event_id' => $livewire->getNomination()->getKey(),
            ])
            ->visible(condition: $this->canImport());
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
            ->visible(condition:$this->canDelete());
    }
}
