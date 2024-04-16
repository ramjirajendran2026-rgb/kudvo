<?php

namespace App\Forms;

use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasNomination;
use App\Models\Election;
use App\Models\Nomination;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Validation\Rules\Unique;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class ElectorForm
{
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

    public static function groupsComponent(): TagsInput
    {
        return TagsInput::make(name: 'groups')
            ->label(label:'Groups')
            ->separator();
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
            ->label(label: 'Membership number')
            ->maxLength(length: 50)
            ->required()
            ->unique(
                ignoreRecord: true,
                modifyRuleUsing: fn (HasElection|HasNomination $livewire, Unique $rule): Unique => $rule
                    ->when(
                        value: $livewire instanceof HasElection,
                        callback: fn (Unique $rule): Unique => $rule
                            ->where(column: 'event_type', value: Election::class)
                            ->where(column: 'event_id', value: $livewire->getElection()->getKey())
                    )
                    ->when(
                        value: $livewire instanceof HasNomination,
                        callback: fn (Unique $rule): Unique => $rule
                            ->where(column: 'event_type', value: Nomination::class)
                            ->where(column: 'event_id', value: $livewire->getNomination()->getKey())
                    )
            );
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: 'Phone number')
            ->validateFor();
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->datalist(options: ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'])
            ->label(label: 'Salutation')
            ->maxLength(length: 20);
    }

    public static function segmentsComponent()
    {
        return Select::make(name: 'segments')
            ->relationship(name: 'segments', titleAttribute: 'name')
            ->createOptionAction(
                callback: fn (Action $action) => $action
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalHeading(heading: 'Create Segment')
                    ->modalWidth(width: MaxWidth::Large)
            )
            ->createOptionUsing(callback: function (array $data, HasElection $livewire) {
                return $livewire->getElection()->segments()->createOrFirst($data)->getKey();
            })
            ->editOptionAction(
                callback: fn (Action $action) => $action
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalWidth(width: MaxWidth::Large)
            )
            ->manageOptionForm(schema: fn (Form $form) => $form->schema([
                TextInput::make(name: 'name')
                    ->label(label: 'Segment name')
                    ->maxLength(length: 150)
                    ->required(),
            ]))
            ->multiple()
            ->preload();
    }
}
