<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Base\Http\Middleware\IdentifyNomination;
use App\Filament\Nomination\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Nomination\NominationPanel;
use App\Filament\Nomination\Pages\Auth\Login;
use App\Settings\GoogleTagManagerSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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
            ->colors(colors: [
                'primary' => Color::hex(color: '#00adb5'),
            ])
            ->font(family: 'Poppins')
            ->viteTheme(theme: 'resources/css/filament/nomination/theme.css')
            ->navigation(builder: false)
            ->databaseNotifications(condition: false)
            ->breadcrumbs(condition: false)
            ->maxContentWidth(maxContentWidth: MaxWidth::FiveExtraLarge)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): HtmlString => $this->getBrandLogo())
            ->brandLogoHeight(height: 'auto')
            ->renderHook(
                name: PanelsRenderHook::HEAD_START,
                hook: fn (GoogleTagManagerSettings $gtm) => new HtmlString(html: $gtm->getHeadScript())
            )
            ->renderHook(
                name: PanelsRenderHook::BODY_START,
                hook: fn (GoogleTagManagerSettings $gtm) => new HtmlString(html: $gtm->getBodyScript())
            )
            ->renderHook(
                name: 'panels::footer',
                hook: fn () => Blade::render('<x-filament.nomination.footer />')
            )
            ->plugins(plugins: [
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(defaultLocales: array_keys(config('laravellocalization.supportedLocales'))),
            ]);
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
    class="text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white"
>
    $organisation->name
</div>
HTML
        );
    }
}
