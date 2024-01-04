<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Nomination\Http\Middleware\IdentifyNomination;
use App\Filament\Nomination\Pages\Auth\Login;
use App\Filament\Nomination\Pages\Dashboard;
use App\Filament\NominationPanel;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class NominationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return NominationPanel::make()
            ->id(id: 'nomination')
            ->path(path: 'nomination/{nomination}')
            ->authGuard(guard: 'elector')
            ->discoverResources(in: app_path('Filament/Nomination/Resources'), for: 'App\\Filament\\Nomination\\Resources')
            ->discoverPages(in: app_path('Filament/Nomination/Pages'), for: 'App\\Filament\\Nomination\\Pages')
            ->pages(pages: [
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Nomination/Widgets'), for: 'App\\Filament\\Nomination\\Widgets')
            ->widgets(widgets: [
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware(middleware: [IdentifyNomination::class], isPersistent: true)
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
            ->navigation(builder: false)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->colors(colors: [
                'primary' => Color::Amber,
            ]);
    }
}
