<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Ballot\Pages\Auth\Login;
use App\Filament\Ballot\Pages\Index;
use App\Filament\BallotPanel;
use App\Filament\Http\Middleware\IdentifyElection;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class BallotPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return BallotPanel::make()
            ->id(id: 'ballot')
            ->path(path: 'ballot')
            ->authGuard(guard: 'elector')
            ->discoverClusters(in: app_path(path: 'Filament/Ballot/Clusters'), for: 'App\\Filament\\Ballot\\Clusters')
            ->discoverResources(in: app_path(path: 'Filament/Ballot/Resources'), for: 'App\\Filament\\Ballot\\Resources')
            ->discoverPages(in: app_path(path: 'Filament/Ballot/Pages'), for: 'App\\Filament\\Ballot\\Pages')
            ->discoverWidgets(in: app_path(path: 'Filament/Ballot/Widgets'), for: 'App\\Filament\\Ballot\\Widgets')
            ->middleware(middleware: [IdentifyElection::class], isPersistent: true)
            ->middleware(middleware: [
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware(middleware: [
                Authenticate::class,
            ])
            ->login(action: Login::class)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): string => Kudvo::getOrganisation()?->getFilamentAvatarUrl())
            ->navigation(builder: false)
            ->colors(colors: [
                'primary' => Color::Amber,
            ]);
    }
}
