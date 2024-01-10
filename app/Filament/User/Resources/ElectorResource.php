<?php

namespace App\Filament\User\Resources;

use App\Filament\Forms\ElectorForm;
use App\Filament\Imports\ElectorImporter;
use App\Models\Elector;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectorResource extends Resource
{
    protected static ?string $model = Elector::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute())
            ->headerActions(actions: [
                static::getTableImportAction(),

                static::getTableCreateAction(),
            ]);
    }

    public static function getTableImportAction(): TableImportAction
    {
        return TableImportAction::make()
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: ElectorImporter::class)
            ->modalFooterActionsAlignment(alignment: Alignment::Center);
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->icon(icon: 'heroicon-m-plus')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->model(model: static::getModel());
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium);
    }
}
