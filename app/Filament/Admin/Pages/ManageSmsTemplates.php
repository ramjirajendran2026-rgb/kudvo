<?php

namespace App\Filament\Admin\Pages;

use App\Notifications\ElectionEulNotification;
use App\Notifications\ElectionMfaNotification;
use App\Settings\SmsTemplates;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ManageSmsTemplates extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SmsTemplates::class;

    protected static ?string $title = 'SMS Templates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: 'OTP SMS')
                    ->headerActions(actions: Arr::map(
                        array: ['CODE' => ElectionMfaNotification::VAR_CODE, 'APP_DOMAIN' => ElectionMfaNotification::VAR_APP_DOMAIN],
                        callback: fn (string $value, string $key) => Forms\Components\Actions\Action::make(name: 'insert'.Str::title($key))
                            ->alpineClickHandler(
                                handler: 'target = document.getElementById(\'data.otp\');$wire.data.otp = target.value.substring(0, target.selectionStart) + \''.$value.'\' + target.value.substring(target.selectionEnd)'
                            )
                            ->color(color: 'info')
                            ->label(label: $key)
                            ->link()
                            ->size(size: ActionSize::Small),
                    ))
                    ->schema(components: [
                        Forms\Components\Textarea::make(name: 'otp')
                            ->autosize()
                            ->hiddenLabel(),
                    ]),

                Forms\Components\Section::make(heading: 'EUL SMS')
                    ->headerActions(actions: Arr::map(
                        array: [
                            'BALLOT_LINK' => ElectionEulNotification::VAR_BALLOT_LINK,
                            'BALLOT_LINK_SHORT' => ElectionEulNotification::VAR_BALLOT_LINK_SHORT,
                            'ELECTION_NAME' => ElectionEulNotification::VAR_ELECTION_NAME,
                            'ELECTION_NAME_SHORT' => ElectionEulNotification::VAR_ELECTION_NAME_SHORT,
                            'ELECTOR_NAME' => ElectionEulNotification::VAR_ELECTOR_NAME,
                            'ELECTOR_NAME_SHORT' => ElectionEulNotification::VAR_ELECTOR_NAME_SHORT,
                        ],
                        callback: fn (string $value, string $key) => Forms\Components\Actions\Action::make(name: 'insert'.Str::title($key))
                            ->alpineClickHandler(
                                handler: 'target = document.getElementById(\'data.eul\');$wire.data.eul = target.value.substring(0, target.selectionStart) + \''.$value.'\' + target.value.substring(target.selectionEnd)'
                            )
                            ->color(color: 'info')
                            ->label(label: $key)
                            ->link()
                            ->size(size: ActionSize::Small),
                    ))
                    ->schema(components: [
                        Forms\Components\Textarea::make(name: 'eul')
                            ->autosize()
                            ->hiddenLabel(),
                    ]),
            ]);
    }
}
