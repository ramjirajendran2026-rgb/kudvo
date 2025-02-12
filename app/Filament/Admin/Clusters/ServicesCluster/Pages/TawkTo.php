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
                        Forms\Components\Toggle::make('enabled'),

                        Forms\Components\Textarea::make('script')
                            ->label('Script')
                            ->requiredIf('enabled', true)
                            ->validationMessages([
                                'required_if' => 'The :attribute field is required when service is enabled.',
                            ]),
                    ]),
            ]);
    }
}
