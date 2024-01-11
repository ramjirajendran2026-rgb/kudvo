<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\User\Resources\ElectionResource;
use Filament\Resources\Pages\Page;

class Ballot extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.positions';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box';
}
