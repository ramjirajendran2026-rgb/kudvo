<?php

namespace App\Filament\Admin\Clusters\SmsSettingsCluster\Pages;

use App\Enums\SmsChannel;
use App\Filament\Admin\Clusters\SmsSettingsCluster;
use App\Forms\Components\CountryPicker;
use App\Settings\SmsSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class CountryMapping extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $activeNavigationIcon = 'heroicon-s-globe-alt';

    protected static string $settings = SmsSettings::class;

    protected static ?string $cluster = SmsSettingsCluster::class;

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
