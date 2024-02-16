<?php

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Filament\Admin\Clusters\Settings;
use App\Notifications\Election\MfaCodeNotification;
use App\Notifications\Election\BallotLinkNotification;
use App\Settings\SmsTemplates;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ManageSmsTemplates extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static string $settings = SmsTemplates::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $title = 'SMS Templates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: 'Ballot Link')
                    ->compact()
                    ->headerActions(actions: Arr::map(
                        array: [
                            'BALLOT_LINK' => BallotLinkNotification::VAR_BALLOT_LINK_SHORT,
                            'ELECTION_NAME' => BallotLinkNotification::VAR_ELECTION_NAME_SHORT,
                            'ELECTOR_NAME' => BallotLinkNotification::VAR_ELECTOR_NAME_SHORT,
                        ],
                        callback: fn (string $value, string $key) => Forms\Components\Actions\Action::make(name: 'insert'.Str::title($key))
                            ->alpineClickHandler(
                                handler: 'target = document.getElementById(\'data.elector_ballot_link\');$wire.data.elector_ballot_link = target.value.substring(0, target.selectionStart) + \''.$value.'\' + target.value.substring(target.selectionEnd)'
                            )
                            ->color(color: 'info')
                            ->label(label: $key)
                            ->link()
                            ->size(size: ActionSize::Small),
                    ))
                    ->schema(components: [
                        Forms\Components\Textarea::make(name: 'elector_ballot_link')
                            ->autosize()
                            ->hiddenLabel(),
                    ]),

                Forms\Components\Section::make(heading: 'Ballot MFA')
                    ->compact()
                    ->headerActions(actions: Arr::map(
                        array: ['CODE' => MfaCodeNotification::VAR_CODE, 'APP_DOMAIN' => MfaCodeNotification::VAR_APP_DOMAIN],
                        callback: fn (string $value, string $key) => Forms\Components\Actions\Action::make(name: 'insert'.Str::title($key))
                            ->alpineClickHandler(
                                handler: 'target = document.getElementById(\'data.elector_ballot_mfa\');$wire.data.elector_ballot_mfa = target.value.substring(0, target.selectionStart) + \''.$value.'\' + target.value.substring(target.selectionEnd)'
                            )
                            ->color(color: 'info')
                            ->label(label: $key)
                            ->link()
                            ->size(size: ActionSize::Small),
                    ))
                    ->schema(components: [
                        Forms\Components\Textarea::make(name: 'elector_ballot_mfa')
                            ->autosize()
                            ->hiddenLabel(),
                    ]),

                Forms\Components\Section::make(heading: 'Nomination MFA')
                    ->compact()
                    ->headerActions(actions: Arr::map(
                        array: ['CODE' => MfaCodeNotification::VAR_CODE, 'APP_DOMAIN' => MfaCodeNotification::VAR_APP_DOMAIN],
                        callback: fn (string $value, string $key) => Forms\Components\Actions\Action::make(name: 'insert'.Str::title($key))
                            ->alpineClickHandler(
                                handler: 'target = document.getElementById(\'data.elector_nomination_mfa\');$wire.data.elector_nomination_mfa = target.value.substring(0, target.selectionStart) + \''.$value.'\' + target.value.substring(target.selectionEnd)'
                            )
                            ->color(color: 'info')
                            ->label(label: $key)
                            ->link()
                            ->size(size: ActionSize::Small),
                    ))
                    ->schema(components: [
                        Forms\Components\Textarea::make(name: 'elector_nomination_mfa')
                            ->autosize()
                            ->hiddenLabel(),
                    ]),
            ]);
    }
}
