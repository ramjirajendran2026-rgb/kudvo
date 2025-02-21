<?php

namespace App\Filament\User\Resources;

use App\Filament\Imports\ParticipantImporter;
use App\Models\Participant;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isDiscovered = false;

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->description(description: fn (Participant $record): ?string => $record->membership_number)
                    ->icon(fn (Participant $participant): ?string => $participant->is_voted ? 'heroicon-s-shield-check' : null)
                    ->iconColor('success')
                    ->iconPosition(IconPosition::After)
                    ->searchable(condition: ['name', 'membership_number'])
                    ->wrap(),

                TextColumn::make(name: 'email')
                    ->searchable(),

                PhoneColumn::make(name: 'phone')
                    ->displayFormat(format: PhoneInputNumberType::E164),

                TextColumn::make(name: 'weightage')
                    ->alignCenter()
                    ->numeric(),

                TextColumn::make(name: 'voted_at')
                    ->dateTimeTooltip('d M Y h:i:s A (T)')
                    ->since()
                    ->timezone(fn ($livewire) => $livewire->getRecord()->timezone),
            ])
            ->emptyStateDescription(description: null);
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
            ->email()
            ->maxLength(length: 150)
            ->requiredWithout('phone');
    }

    public static function getPhoneFormComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->defaultCountry(Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
            ->disableIpLookUp()
            ->initialCountry(Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
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
