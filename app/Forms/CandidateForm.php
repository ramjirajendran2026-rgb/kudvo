<?php

namespace App\Forms;

use App\Filament\Contracts\HasElection;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Nominee;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
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

    public static function candidateGroupIdComponent(): Select
    {
        return Select::make(name: 'candidate_group_id')
            ->createOptionForm(schema: [
                TextInput::make(name: 'name')
                    ->label(label: 'Group name')
                    ->maxLength(length: 100)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),

                TextInput::make(name: 'short_name')
                    ->label(label: 'Short name')
                    ->maxLength(length: 10)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),

                Hidden::make(name: 'election_id')
                    ->dehydrateStateUsing(callback: fn(HasElection $livewire) => $livewire->getElection()->getKey())
            ])
            ->placeholder(placeholder: 'Choose a group')
            ->preload()
            ->relationship(
                name: 'candidateGroup',
                titleAttribute: 'short_name',
                modifyQueryUsing: fn (HasElection $livewire, $query) => $query->where('election_id', $livewire->getElection()->getKey())
            )
            ->searchable()
            ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_group);
    }

    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: 'Email address')
            ->maxLength(length: 100)
            ->placeholder(placeholder: 'Email address')
            ->rule(rule: 'email:rfc,dns');
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: 'First name')
            ->maxLength(length: 100)
            ->requiredWithout(statePaths: ['last_name']);
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
            ->afterStateUpdated(callback: function (Set $set, ?string $state, HasElection $livewire): void {
                $elector = blank(value: $state) ?
                    null :
                    $livewire->getElection()->electors()->firstWhere('membership_number', $state);

                $set(path: 'elector_id', state: $elector?->getKey());
                $set(path: 'title', state: $elector?->title);
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
            ->placeholder(placeholder: 'Membership number')
            ->validationMessages(messages: [
                'exists' => 'This :attribute is not found in electors data',
            ]);
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: 'Phone number')
            ->validateFor();
    }

    public static function photoComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'photo')
            ->avatar()
            ->circleCropper()
            ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
            ->imageEditor()
            ->placeholder(placeholder: 'Drag & Drop your photo or <span class="filepond--label-action">Browse</span>');
    }

    public static function positionIdComponent(): Select
    {
        return Select::make(name: 'position_id')
            ->hiddenLabel()
            ->native(condition: false)
            ->placeholder(placeholder: 'Choose a position')
            ->required();
    }

    public static function symbolComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'symbol')
            ->avatar()
            ->circleCropper()
            ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
            ->imageEditor()
            ->placeholder(placeholder: 'Drag & Drop your symbol or <span class="filepond--label-action">Browse</span>');
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->datalist(options: ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'])
            ->label(label: 'Salutation')
            ->maxLength(length: 20);
    }
}
