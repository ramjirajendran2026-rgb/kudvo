<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\Login;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(id: 'admin')
            ->domain(domain: config('app.admin_panel.domain'))
            ->path(path: config('app.admin_panel.prefix'))
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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
            ->unsavedChangesAlerts()
            ->navigationGroups(groups: [
                NavigationGroup::make()
                    ->label(label: 'Election'),
            ])
            ->colors(colors: [
                'primary' => Color::Amber,
            ])
            ->font(
                family: 'Poppins',
                provider: LocalFontProvider::class,
            )
            ->viteTheme(theme: 'resources/css/filament/admin/theme.css')
            ->plugins([
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(defaultLocales: array_keys(config('laravellocalization.supportedLocales'))),
                ActivitylogPlugin::make(),
            ]);
    }
}
