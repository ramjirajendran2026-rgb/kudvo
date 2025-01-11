<?php

namespace App\Filament\User\Resources;

use App\Filament\Imports\ParticipantImporter;
use App\Models\Participant;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static bool $isDiscovered = false;

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute());
    }

    public static function getNameFormComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->maxLength(length: 150)
            ->required();
    }

    public static function getMembershipNumberFormComponent(): TextInput
    {
        return TextInput::make(name: 'membership_number')
            ->maxLength(length: 150);
    }

    public static function getEmailFormComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->maxLength(length: 150)
            ->email();
    }

    public static function getPhoneFormComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->validateFor();
    }

    public static function getWeightageFormComponent(): TextInput
    {
        return TextInput::make(name: 'weightage')
            ->default(state: 1)
            ->minValue(value: 0)
            ->numeric()
            ->required();
    }

    public static function getCreateTableAction(): CreateAction
    {
        return CreateAction::make();
    }

    public static function getImportTableAction(): ImportAction
    {
        return ImportAction::make()
            ->icon(icon: 'heroicon-s-arrow-up-tray')
            ->importer(importer: ParticipantImporter::class);
    }

    public static function getEditTableAction(): EditAction
    {
        return EditAction::make()
            ->iconButton();
    }

    public static function getDeleteTableAction(): DeleteAction
    {
        return DeleteAction::make()
            ->iconButton();
    }

    public static function getDeleteBulkAction(): DeleteBulkAction
    {
        return DeleteBulkAction::make();
    }
}
