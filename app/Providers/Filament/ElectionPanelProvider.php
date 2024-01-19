<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Election\Http\Middleware\AuthenticateSession;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Http\Middleware\IdentifyElection;
use App\Filament\Election\Pages\Auth\Login;
use App\Filament\ElectionPanel;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ElectionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return ElectionPanel::make()
            ->id(id: 'election')
            ->path(path: 'election')
            ->authGuard(guard: 'elector')
            ->discoverResources(in: app_path('Filament/Election/Resources'), for: 'App\\Filament\\Election\\Resources')
            ->discoverPages(in: app_path('Filament/Election/Pages'), for: 'App\\Filament\\Election\\Pages')
            ->discoverWidgets(in: app_path('Filament/Election/Widgets'), for: 'App\\Filament\\Election\\Widgets')
            ->widgets(widgets: [
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
                EnsureMfaCompleted::class,
            ])
            ->login(action: Login::class)
            ->colors(colors: [
                'primary' => Color::Amber,
            ])
            ->font(family: 'Poppins')
            ->viteTheme('resources/css/filament/election/theme.css')
            ->navigation(builder: false)
            ->databaseNotifications(condition: false)
            ->breadcrumbs(condition: false)
            ->maxContentWidth(maxContentWidth: MaxWidth::FiveExtraLarge)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): string => Kudvo::getOrganisation()?->getFilamentAvatarUrl())
            ->renderHook(
                name: 'panels::footer',
                hook: fn () => Blade::render('<x-filament.nomination.footer />')
            );
    }
}
