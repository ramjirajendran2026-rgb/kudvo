<?php

namespace App\Filament\Admin\Clusters\SiteSettings\Pages;

use App\Filament\Admin\Clusters\SiteSettings;
use App\Settings\GoogleTagManagerSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class GoogleTagManager extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GoogleTagManagerSettings::class;

    protected static ?string $cluster = SiteSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema(components: [
                        Forms\Components\TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
