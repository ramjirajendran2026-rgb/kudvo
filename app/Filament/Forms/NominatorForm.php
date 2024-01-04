<?php

namespace App\Filament\Forms;

use App\Filament\Nomination\Pages\Contracts\HasElector;
use App\Filament\Nomination\Pages\Contracts\HasNomination;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class NominatorForm
{
    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: 'Email address')
            ->maxLength(length: 100);
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

                $set(path: 'first_name', state: $elector?->first_name);
                $set(path: 'last_name', state: $elector?->last_name);
                $set(path: 'email', state: $elector?->email);
                $set(path: 'phone', state: $elector?->phone);
            })
            ->distinct()
            ->exists(
                table: 'electors',
                column: 'membership_number',
                modifyRuleUsing: fn (Exists $rule, HasNomination $livewire) => $rule
                    ->where(column: 'event_type', value: Nomination::class)
                    ->where(column: 'event_id', value: $livewire->getNomination()->getKey())
            )
            ->in(
                values: fn (HasElector $livewire): string => $livewire->getElector()->membership_number,
                condition: fn (TextInput $component, HasNomination $livewire): bool => ! $livewire->getNomination()->self_nomination &&
                    Arr::last(explode('.', $component->getStatePath()), fn($item) => $item != $component->getStatePath(isAbsolute: false)) == 0,
            )
            ->live(onBlur: true)
            ->label(label: 'Membership number')
            ->maxLength(length: 50)
            ->notIn(
                values: fn (HasElector $livewire): string => $livewire->getElector()->membership_number,
                condition: fn (TextInput $component, HasNomination $livewire): bool => ! $livewire->getNomination()->self_nomination &&
                    Arr::last(explode('.', $component->getStatePath()), fn($item) => $item != $component->getStatePath(isAbsolute: false)) != 0,
            )
            ->readOnly(
                condition: fn (TextInput $component, HasNomination $livewire): bool => ! $livewire->getNomination()->self_nomination &&
                    Arr::last(explode('.', $component->getStatePath()), fn($item) => $item != $component->getStatePath(isAbsolute: false)) == 0
            )
            ->required()
            ->validationMessages(messages: [
                'not_in' => 'Nominee cannot be a nominator'
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

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->label(label: 'Salutation')
            ->maxLength(length: 20);
    }
}
