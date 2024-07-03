<?php

namespace App\Filament\Admin\Clusters\SmsSettingsCluster\Pages;

use App\Data\ClicksendConfigData;
use App\Data\TwentyFourSevenSmsConfigData;
use App\Filament\Admin\Clusters\SmsSettingsCluster;
use App\Settings\ServiceConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class Services extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $activeNavigationIcon = 'heroicon-s-key';

    protected static string $settings = ServiceConfig::class;

    protected static ?string $cluster = SmsSettingsCluster::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: '24x7 SMS')
                    ->collapsible()
                    ->compact()
                    ->mutateDehydratedStateUsing(callback: fn (array $state): TwentyFourSevenSmsConfigData => TwentyFourSevenSmsConfigData::from($state))
                    ->statePath(path: 'twenty_four_seven_sms')
                    ->schema(components: [
                        Forms\Components\TextInput::make(name: 'api_key')
                            ->password()
                            ->revealable(),

                        Forms\Components\TextInput::make(name: 'sender_id'),
                    ]),

                Forms\Components\Section::make(heading: 'Clicksend')
                    ->collapsible()
                    ->compact()
                    ->mutateDehydratedStateUsing(callback: fn (array $state): ClicksendConfigData => ClicksendConfigData::from($state))
                    ->statePath(path: 'clicksend')
                    ->schema(components: [
                        Forms\Components\TextInput::make(name: 'username'),

                        Forms\Components\TextInput::make(name: 'api_key')
                            ->password()
                            ->revealable(),
                    ]),
            ]);
    }
}
