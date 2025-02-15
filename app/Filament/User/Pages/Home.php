<?php

namespace App\Filament\User\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class Home extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.user.pages.home';

    protected static ?string $slug = '/';

    public function getHeading(): string | Htmlable
    {
        return Filament::getTenant()?->name ?? '';
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenLarge;
    }
}
