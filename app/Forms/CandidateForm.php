<?php

namespace App\Forms;

use App\Filament\Base\Contracts\HasElection;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class CandidateForm
{
    public static function attachmentComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'attachments')
            ->collection(collection: Candidate::MEDIA_COLLECTION_ATTACHMENTS)
            ->label(label: __('filament.user.candidate-resource.form.attachments.label'))
            ->maxFiles(count: 5)
            ->maxSize(size: 1024 * 2)
            ->multiple()
            ->reorderable();
    }

    public static function bioComponent(): Textarea
    {
        return Textarea::make(name: 'bio')
            ->label(label: 'Bio')
            ->maxLength(length: 10000)
            ->placeholder(placeholder: 'Bio');
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
            )
            ->label(label: __('filament.user.candidate-resource.form.elector_id.label'));
    }

    public static function candidateGroupIdComponent(): Select
    {
        return Select::make(name: 'candidate_group_id')
            ->createOptionForm(schema: [
                TextInput::make(name: 'name')
                    ->label(label: __('filament.user.candidate-resource.form.candidate_group_id.form.name.label'))
                    ->maxLength(length: 100)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),

                TextInput::make(name: 'short_name')
                    ->label(label: __('filament.user.candidate-resource.form.candidate_group_id.form.short_name.label'))
                    ->maxLength(length: 10)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),

                Hidden::make(name: 'election_id')
                    ->dehydrateStateUsing(callback: fn (HasElection $livewire) => $livewire->getElection()->getKey()),
            ])
            ->editOptionForm(schema: [
                TextInput::make(name: 'name')
                    ->label(label: __('filament.user.candidate-resource.form.candidate_group_id.form.name.label'))
                    ->maxLength(length: 100)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),

                TextInput::make(name: 'short_name')
                    ->label(label: __('filament.user.candidate-resource.form.candidate_group_id.form.short_name.label'))
                    ->maxLength(length: 10)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, HasElection $livewire) => $rule
                            ->where(column: 'election_id', value: $livewire->getElection()->getKey())
                    ),
            ])
            ->label(label: __('filament.user.candidate-resource.form.candidate_group_id.label'))
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.candidate_group_id.placeholder'))
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
            ->label(label: __('filament.user.candidate-resource.form.email.label'))
            ->maxLength(length: 100)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.email.placeholder'))
            ->rule(rule: 'email:rfc,dns');
    }

    public static function fallbackPositionsComponent(?Position $position = null): Repeater
    {
        return Repeater::make(name: 'fallbackPositions')
            ->label(label: __('filament.user.candidate-resource.form.fallback_positions.label'))
            ->relationship()
            ->simple(
                field: Select::make(name: 'position_id')
                    ->label(label: 'Position')
                    ->relationship(
                        name: 'position',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, HasElection $livewire) => $query
                            ->when(
                                value: $position,
                                callback: fn (Builder $query) => $query
                                    ->whereKeyNot($position->getKey())
                                    ->where('sort', '>', $position->sort),
                            )
                            ->whereMorphedTo('event', $livewire->getElection()),
                    )
                    ->searchable()
                    ->preload()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->getOptionLabelFromRecordUsing(fn (Position $position) => $position->name)
                    ->placeholder('Select fallback position'),
            )
            ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->waterfall_voting);
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: __('filament.user.candidate-resource.form.first_name.label'))
            ->maxLength(length: 100)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.first_name.placeholder'))
            ->requiredWithout(statePaths: ['last_name']);
    }

    public static function lastNameComponent(): TextInput
    {
        return TextInput::make(name: 'last_name')
            ->label(label: __('filament.user.candidate-resource.form.last_name.label'))
            ->maxLength(length: 100)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.last_name.placeholder'));
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
            ->helperText(text: __('filament.user.candidate-resource.form.membership_number.helper_text'))
            ->label(label: __('filament.user.candidate-resource.form.membership_number.label'))
            ->maxLength(length: 50)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.membership_number.placeholder'))
            ->validationMessages(messages: [
                'exists' => __('filament.user.candidate-resource.form.membership_number.validation.exists'),
            ]);
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: __('filament.user.candidate-resource.form.phone.label'))
            ->validateFor();
    }

    public static function photoComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'photo')
            ->avatar()
            ->circleCropper()
            ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
            ->extraAttributes(attributes: ['class' => 'candidate-photo'])
            ->imageEditor()
            ->label(label: __('filament.user.candidate-resource.form.photo.label'))
            ->maxSize(size: 1024)
            ->panelAspectRatio(ratio: '1:1')
            ->panelLayout(layout: 'compact')
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.photo.placeholder'));
    }

    public static function positionIdComponent(): Select
    {
        return Select::make(name: 'position_id')
            ->hiddenLabel()
            ->label(label: __('filament.user.candidate-resource.form.position_id.label'))
            ->native(condition: false)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.position_id.placeholder'))
            ->required();
    }

    public static function symbolComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'symbol')
            ->avatar()
            ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
            ->extraAttributes(attributes: ['class' => 'candidate-symbol'])
            ->imageEditor()
            ->label(label: __('filament.user.candidate-resource.form.symbol.label'))
            ->maxSize(size: 1024)
            ->panelAspectRatio(ratio: '1:1')
            ->panelLayout(layout: 'compact')
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.symbol.placeholder'));
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->datalist(options: ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'])
            ->label(label: __('filament.user.candidate-resource.form.title.label'))
            ->maxLength(length: 20)
            ->placeholder(placeholder: __('filament.user.candidate-resource.form.title.placeholder'));
    }
}
