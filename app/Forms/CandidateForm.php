<?php

namespace App\Forms;

use App\Filament\Contracts\HasElection;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Nominee;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Validation\Rules\Exists;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class CandidateForm
{
    public static function attachmentComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'attachments')
            ->collection(collection: Candidate::MEDIA_COLLECTION_ATTACHMENTS)
            ->maxFiles(count: 5)
            ->maxSize(size: 1024 * 2)
            ->multiple()
            ->reorderable();
    }

    public static function bioComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'bio')
            ->collection(collection: Candidate::MEDIA_COLLECTION_BIO)
            ->maxSize(size: 1024 * 2);
    }

    public static function electorIdComponent(): Hidden
    {
        return Hidden::make(name: 'elector_id')
            ->exists(
                table: 'electors',
                column: 'id',
                modifyRuleUsing: fn (Exists $rule, HasElection $livewire) => $rule
                    ->where(column: 'event_type', value: Election::class)
                    ->where(column: 'event_id', value: $livewire->getElection()->getKey())
            );
    }

    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: 'Email address')
            ->maxLength(length: 100)
            ->rule(rule: 'email:rfc,dns');
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: 'First name')
            ->maxLength(length: 100);
    }

    public static function lastNameComponent(): TextInput
    {
        return TextInput::make(name: 'last_name')
            ->label(label: 'Last name')
            ->maxLength(length: 100);
    }

    public static function membershipNumberComponent(): TextInput
    {
        return TextInput::make(name: 'membership_number')
            ->afterStateUpdated(callback: function (Set $set, ?string $state): void {
                $elector = blank(value: $state) ?
                    null :
                    Elector::firstWhere('membership_number', $state);

                $set(path: 'elector_id', state: $elector?->getKey());
                $set(path: 'first_name', state: $elector?->first_name);
                $set(path: 'last_name', state: $elector?->last_name);
                $set(path: 'email', state: $elector?->email);
                $set(path: 'phone', state: $elector?->phone);
            })
            ->exists(
                table: 'electors',
                column: 'membership_number',
                modifyRuleUsing: fn (Exists $rule, HasElection $livewire) => $rule
                    ->where(column: 'event_type', value: Election::class)
                    ->where(column: 'event_id', value: $livewire->getElection()->getKey())
            )
            ->label(label: 'Membership number')
            ->maxLength(length: 50)
            ->validationMessages(messages: [
                'exists' => 'This :attribute is not found in electors data',
            ]);
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->defaultCountry(value: config(key: 'app.default_phone_country'))
            ->label(label: 'Phone number')
            ->useFullscreenPopup()
            ->validateFor();
    }

    public static function photoComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'photo')
            ->avatar()
            ->circleCropper()
            ->collection(collection: Nominee::MEDIA_COLLECTION_PHOTO)
            ->imageEditor();
    }

    public static function positionIdComponent(): Select
    {
        return Select::make(name: 'position_id')
            ->hiddenLabel()
            ->native(condition: false)
            ->placeholder(placeholder: 'Choose a position')
            ->required();
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->datalist(options: ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'])
            ->label(label: 'Salutation')
            ->maxLength(length: 20);
    }
}
