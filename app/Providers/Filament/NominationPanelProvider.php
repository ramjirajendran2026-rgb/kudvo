<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Nomination\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Nomination\Http\Middleware\IdentifyNomination;
use App\Filament\Nomination\Pages\Auth\Login;
use App\Filament\Nomination\Pages\Dashboard;
use App\Filament\NominationPanel;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class NominationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return NominationPanel::make()
            ->id(id: 'nomination')
            ->path(path: 'nomination')
            ->authGuard(guard: 'elector')
            ->discoverResources(in: app_path('Filament/Nomination/Resources'), for: 'App\\Filament\\Nomination\\Resources')
            ->discoverPages(in: app_path('Filament/Nomination/Pages'), for: 'App\\Filament\\Nomination\\Pages')
            ->discoverWidgets(in: app_path('Filament/Nomination/Widgets'), for: 'App\\Filament\\Nomination\\Widgets')
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
                EnsureMfaCompleted::class,
            ])
            ->login(action: Login::class)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->colors(colors: [
                'primary' => Color::hex(color: '#00adb5'),
                'warning' => Color::Yellow,
            ])
            ->navigation(builder: false)
            ->databaseNotifications(condition: false)
            ->maxContentWidth(maxContentWidth: MaxWidth::FiveExtraLarge)
            ->viteTheme(theme: 'resources/css/filament/nomination/theme.css')
            ->font(family: 'Poppins')
            ->breadcrumbs(condition: false)
            ->renderHook(
                name: 'panels::footer',
                hook: fn () => Blade::render('<x-filament.nomination.footer />')
            );
    }
}
