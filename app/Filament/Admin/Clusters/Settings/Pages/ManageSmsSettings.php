<?php

namespace App\Filament\Admin\Clusters\Settings\Pages;

use App\Enums\SmsChannel;
use App\Filament\Admin\Clusters\Settings;
use App\Forms\Components\CountryPicker;
use App\Settings\SmsSettings;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSmsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-left-ellipsis';

    protected static string $settings = SmsSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $title = 'SMS Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: 'Default channel')
                    ->compact()
                    ->schema(components: [
                        Forms\Components\ToggleButtons::make(name: 'default_channel')
                            ->hiddenLabel()
                            ->inline()
                            ->options(options: SmsChannel::class)
                            ->required(),
                    ]),

                Forms\Components\Section::make(heading: 'Country-wise channel')
                    ->compact()
                    ->schema(components: [
                        Forms\Components\Repeater::make(name: 'country_channel')
                            ->addActionLabel(label: 'Add another country')
                            ->grid()
                            ->hiddenLabel()
                            ->reorderable(condition: false)
                            ->schema(components: [
                                CountryPicker::make()
                                    ->hiddenLabel()
                                    ->placeholder(placeholder: 'Select a country')
                                    ->required(),

                                Forms\Components\ToggleButtons::make(name: 'channel')
                                    ->hiddenLabel()
                                    ->inline()
                                    ->options(options: SmsChannel::class)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
