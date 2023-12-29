<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Resources\NominationResource;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Positions extends NominationPage
{
    protected static string $view = 'filament.resources.nomination-resource.pages.positions';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $activeNavigationIcon = 'heroicon-s-briefcase';

    public static function getNavigationLabel(): string
    {
        return __(key: 'filament/resources/nomination.positions.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __(key: 'filament/resources/nomination.positions.title');
    }
}
