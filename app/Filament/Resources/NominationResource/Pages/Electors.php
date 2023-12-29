<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Forms\ElectorForm;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Actions\CreateAction;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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
        return __(key: 'filament/resources/nomination.electors.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __(key: 'filament/resources/nomination.electors.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                ElectorForm::membershipNumberComponent(),

                ElectorForm::firstNameComponent(),

                ElectorForm::lastNameComponent(),

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
            ])
            ->columns(components: [
                TextColumn::make(name: 'membership_number'),

                TextColumn::make(name: 'full_name'),

                TextColumn::make(name: 'phone'),

                TextColumn::make(name: 'email'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->createAction(),
        ];
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
            ->model(model: Elector::class)
            ->relationship(relationship: static fn (self $livewire): Relation => $livewire->nomination->electors());
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
}
