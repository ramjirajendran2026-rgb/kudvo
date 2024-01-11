<?php

namespace App\Providers\Filament;

use App\Filament\User\Pages\Auth\EditProfile;
use App\Filament\User\Pages\Auth\Register;
use App\Filament\User\Pages\Organisation\EditOrganisationProfile;
use App\Filament\User\Pages\Organisation\EditProfile as OrganisationEditProfile;
use App\Filament\User\Pages\Organisation\Register as RegisterOrganisation;
use App\Models\Organisation;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
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
            ->colors(colors: [
                'primary' => Color::hex(color: '#00adb5'),
            ])
            ->font(family: 'Poppins')
            ->databaseNotifications()
            ->globalSearch(provider: false)
            ->topNavigation()
            ->spa();
    }
}
