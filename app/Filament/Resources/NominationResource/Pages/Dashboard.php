<?php

namespace App\Filament\Resources\NominationResource\Pages;

use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends NominationPage
{
    protected static string $view = 'filament.resources.nomination-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public static function getNavigationLabel(): string
    {
        return __(key: 'filament/resources/nomination.dashboard.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __(key: 'filament/resources/nomination.dashboard.title');
    }
}
