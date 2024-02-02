<?php

namespace App\Filament\Election\Pages;

use App\Filament\Election\Http\Middleware\EnsureDeviceIsAllowed;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Page;

class DeviceAlreadyUsed extends Page
{
    use InteractsWithElection;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.election.pages.device-already-used';

    protected static string | array $withoutRouteMiddleware = [
        EnsureDeviceIsAllowed::class,
        Authenticate::class,
        EnsureMfaCompleted::class,
    ];
}
