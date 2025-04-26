<?php

namespace App\Filament\Admin\Clusters\ServicesCluster\Pages;

use App\Data\TawkToConfigData;
use App\Filament\Admin\Clusters\ServicesCluster;
use App\Settings\ServiceConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TawkTo extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-left-right';

    protected static string $settings = ServiceConfig::class;

    protected static ?string $cluster = ServicesCluster::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->heading('tawk.to')
                    ->mutateDehydratedStateUsing(callback: fn (array $state): TawkToConfigData => TawkToConfigData::from($state))
                    ->statePath('tawk_to')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->live(),

                        Forms\Components\Textarea::make('script')
                            ->label('Script')
                            ->requiredIf('enabled', true)
                            ->validationMessages([
                                'required_if' => 'The :attribute field is required when service is enabled.',
                            ]),

                        Forms\Components\Fieldset::make('Show on')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Toggle::make('home_page')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('product_pages')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('wiki_pages')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('user_panel')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('election_panel')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('meeting_panel')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),

                                Forms\Components\Toggle::make('nomination_panel')
                                    ->disabled(fn (Forms\Get $get): bool => ! $get('enabled')),
                            ]),
                    ]),
            ]);
    }
}
