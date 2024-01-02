<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Forms\ElectorForm;
use App\Filament\Imports\ElectorImporter;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms\Components\Group;
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
        return $this->nomination;
    }

    public static function getNavigationLabel(): string
    {
        return 'Electors';
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
                $this->editAction(),

                $this->deleteAction(),
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
                    ->wrap(),
            ])
            ->headerActions(actions: [
                $this->importAction(),

                $this->createAction(),
            ]);
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
            ->model(model: Elector::class);
    }

    protected function importAction(): ImportAction
    {
        return ImportAction::make()
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: ElectorImporter::class)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->options(options: fn (self $livewire): array => [
                'event_type' => Nomination::class,
                'event_id' => $livewire->nomination->getKey(),
            ]);
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
