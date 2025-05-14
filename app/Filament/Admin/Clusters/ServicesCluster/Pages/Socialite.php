<?php

namespace App\Filament\Admin\Clusters\ServicesCluster\Pages;

use App\Data\FacebookConfigData;
use App\Data\GithubConfigData;
use App\Data\GoogleConfigData;
use App\Data\LinkedInConfigData;
use App\Data\XConfigData;
use App\Filament\Admin\Clusters\ServicesCluster;
use App\Settings\ServiceConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class Socialite extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $activeNavigationIcon = 'heroicon-s-key';

    protected static string $settings = ServiceConfig::class;

    protected static ?string $cluster = ServicesCluster::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Facebook')
                            ->columns()
                            ->mutateDehydratedStateUsing(fn (array $state): FacebookConfigData => FacebookConfigData::from($state))
                            ->statePath('facebook')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->maxLength(200)
                                    ->password()
                                    ->revealable(),
                            ]),

                        Forms\Components\Fieldset::make('Google')
                            ->columns()
                            ->mutateDehydratedStateUsing(fn (array $state): GoogleConfigData => GoogleConfigData::from($state))
                            ->statePath('google')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->maxLength(200)
                                    ->password()
                                    ->revealable(),
                            ]),

                        Forms\Components\Fieldset::make('Github')
                            ->columns()
                            ->mutateDehydratedStateUsing(fn (array $state): GithubConfigData => GithubConfigData::from($state))
                            ->statePath('github')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->maxLength(200)
                                    ->password()
                                    ->revealable(),
                            ]),

                        Forms\Components\Fieldset::make('LinkedIn')
                            ->columns()
                            ->mutateDehydratedStateUsing(fn (array $state): LinkedInConfigData => LinkedInConfigData::from($state))
                            ->statePath('linkedin')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->maxLength(200)
                                    ->password()
                                    ->revealable(),
                            ]),

                        Forms\Components\Fieldset::make('X')
                            ->columns()
                            ->mutateDehydratedStateUsing(fn (array $state): XConfigData => XConfigData::from($state))
                            ->statePath('x')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->maxLength(200)
                                    ->password()
                                    ->revealable(),
                            ]),
                    ]),
            ]);
    }
}
