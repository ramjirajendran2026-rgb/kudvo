<?php

namespace App\Providers\Filament;

use App\Filament\User\Http\Controllers\ElectionUserInvitationController;
use App\Filament\User\Pages\Auth\EditProfile;
use App\Filament\User\Pages\Auth\Register;
use App\Filament\User\Pages\Organisation\EditOrganisationProfile;
use App\Filament\User\Pages\Organisation\Register as RegisterOrganisation;
use App\Models\Organisation;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id(id: 'user')
            ->path(path: 'app')
            ->discoverResources(in: app_path(path: 'Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path(path: 'Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->discoverWidgets(in: app_path(path: 'Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
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
            ->registration(action: Register::class)
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile(page: EditProfile::class)
            ->tenant(model: Organisation::class)
            ->tenantRegistration(page: RegisterOrganisation::class)
            ->tenantProfile(page: EditOrganisationProfile::class)
            ->authenticatedRoutes(routes: function () {
                Route::get(
                    uri: 'election-collaborators/{invitation}',
                    action: [ElectionUserInvitationController::class, 'accept']
                )->name(name: 'election-collaborators.accept');
            })
            ->colors(colors: [
                'primary' => Color::Violet,
            ])
            ->font(family: 'Poppins')
            ->viteTheme(theme: 'resources/css/filament/user/theme.css')
            ->databaseNotifications()
            ->globalSearch(provider: false)
            ->topNavigation()
            ->breadcrumbs(condition: false)
            ->spa()
            ->plugins(plugins: [
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(defaultLocales: array_keys(config('laravellocalization.supportedLocales'))),
            ]);
    }
}
