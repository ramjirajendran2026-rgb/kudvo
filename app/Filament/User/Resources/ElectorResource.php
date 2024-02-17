<?php

namespace App\Filament\User\Resources;

use App\Filament\Imports\ElectorImporter;
use App\Forms\ElectorForm;
use App\Models\Elector;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentClusters\Forms\Cluster;

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

                Cluster::make(schema: [
                    ElectorForm::titleComponent()
                        ->placeholder(placeholder: 'Title'),

                    ElectorForm::firstNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: 'First name'),

                    ElectorForm::lastNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: 'Last name'),
                ])
                    ->columns(columns: 5)
                    ->label(label: 'Full name'),

                ElectorForm::emailComponent(),

                ElectorForm::phoneComponent()
                    ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                    ->disableIpLookUp()
                    ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),

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
                    ->searchable()
                    ->wrap(),

                TextColumn::make(name: 'phone')
                    ->label(label: 'Phone number')
                    ->searchable(),

                TextColumn::make(name: 'email')
                    ->label(label: 'Email address')
                    ->wrap()
                    ->searchable(),

                TextColumn::make(name: 'groups')
                    ->badge()
                    ->separator()
                    ->wrap(),
            ])
            ->headerActions(actions: [
                static::getTableImportAction(),

                static::getTableCreateAction(),
            ])
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute());
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
            ->form(form: fn (Form $form): Form => static::form($form))
            ->icon(icon: 'heroicon-m-plus')
            ->model(model: static::getModel())
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modelLabel(label: static::getModelLabel())
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->form(form: fn (Form $form): Form => static::form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton();
    }

    public static function getBulkDeleteAction(): DeleteBulkAction
    {
        return DeleteBulkAction::make();
    }
}
