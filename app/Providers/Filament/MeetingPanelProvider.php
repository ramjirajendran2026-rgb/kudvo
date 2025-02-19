<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Base\Http\Middleware\IdentifyMeeting;
use App\Filament\LocalAvatarProvider;
use App\Filament\Meeting\Http\Controllers\UniqueMeetingLinkController;
use App\Filament\Meeting\Http\Middleware\IdentifyPanelState;
use App\Filament\Meeting\MeetingPanel;
use App\Filament\Meeting\Pages\Auth\Login;
use App\Settings\GoogleTagManagerSettings;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MeetingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return MeetingPanel::make()
            ->id('meeting')
            ->path('meet')
            ->authGuard(guard: 'participant')
            ->discoverResources(in: app_path('Filament/Meeting/Resources'), for: 'App\\Filament\\Meeting\\Resources')
            ->discoverPages(in: app_path('Filament/Meeting/Pages'), for: 'App\\Filament\\Meeting\\Pages')
            ->discoverWidgets(in: app_path('Filament/Meeting/Widgets'), for: 'App\\Filament\\Meeting\\Widgets')
            ->middleware(middleware: [IdentifyMeeting::class], isPersistent: true)
            ->middleware([
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
            ->middleware([IdentifyPanelState::class], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->login(action: Login::class)
            ->routes(routes: function () {
                Route::get(uri: '/eul/{participant}', action: UniqueMeetingLinkController::class)
                    ->middleware(middleware: 'signed')
                    ->name(name: 'eul');
            })
            ->viteTheme(theme: [
                'resources/css/filament/meeting/theme.css',
                'resources/js/filament/meeting/scripts.js',
            ])
            ->colors([
                'primary' => Color::Purple,
            ])
            ->font(
                family: 'Poppins',
                provider: LocalFontProvider::class,
            )
            ->defaultAvatarProvider(LocalAvatarProvider::class)
            ->navigation(false)
            ->maxContentWidth(maxContentWidth: MaxWidth::SevenExtraLarge)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): HtmlString => $this->getBrandLogo())
            ->brandLogoHeight(height: 'auto')
            ->darkMode(condition: false)
            ->spa()
            ->renderHook(
                name: PanelsRenderHook::HEAD_START,
                hook: fn (GoogleTagManagerSettings $gtm) => new HtmlString(html: $gtm->getHeadScript())
            )
            ->renderHook(
                name: PanelsRenderHook::BODY_START,
                hook: fn (GoogleTagManagerSettings $gtm) => new HtmlString(html: $gtm->getBodyScript())
            );
    }

    protected function getBrandLogo(): HtmlString
    {
        $organisation = Kudvo::getOrganisation();
        $logoUrl = $organisation->logo_url;

        return new HtmlString(
            html: <<<HTML
<img
    alt="$organisation->name\'s logo"
    src="$logoUrl"
    class="rounded-xl"
/>
<div
    class="text-xl md:text-3xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white"
>
    $organisation->name
</div>
HTML
        );
    }
}
