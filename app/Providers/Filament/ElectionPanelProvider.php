<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Election\Http\Controllers\BoothTokensController;
use App\Filament\Election\Http\Controllers\WebManifestController;
use App\Filament\Election\Http\Middleware\AuthenticateSession;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Http\Middleware\IdentifyBoothToken;
use App\Filament\Election\Http\Middleware\IdentifyPanelState;
use App\Filament\Election\Pages\Auth\Login;
use App\Filament\Election\Pages\Index;
use App\Filament\ElectionPanel;
use App\Filament\Http\Middleware\IdentifyElection;
use App\Models\Elector;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ElectionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return ElectionPanel::make()
            ->id(id: 'election')
            ->path(path: 'el')
            ->authGuard(guard: 'elector')
            ->discoverResources(in: app_path(path: 'Filament/Election/Resources'), for: 'App\\Filament\\Election\\Resources')
            ->discoverPages(in: app_path(path: 'Filament/Election/Pages'), for: 'App\\Filament\\Election\\Pages')
            ->discoverWidgets(in: app_path(path: 'Filament/Election/Widgets'), for: 'App\\Filament\\Election\\Widgets')
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
            ->middleware(middleware: [IdentifyBoothToken::class, IdentifyPanelState::class, EnsureStateIsAllowed::class], isPersistent: true)
            ->authMiddleware(middleware: [
                Authenticate::class,
                EnsureMfaCompleted::class,
            ])
            ->login(action: Login::class)
            ->routes(routes: function (): void {
                Route::get(uri: 'app.webmanifest', action: WebManifestController::class)
                    ->withoutMiddleware(middleware: EnsureStateIsAllowed::class)
                    ->name(name: 'web-app-manifest');

                Route::get(uri: 'booth/{token}/activate', action: [BoothTokensController::class, 'activate'])
                    ->middleware(middleware: 'signed')
                    ->withoutMiddleware(middleware: EnsureStateIsAllowed::class)
                    ->name(name: 'booth.activate');

                Route::get(
                    uri: 'eul/{elector}',
                    action: function (Request $request) {
                        $elector = app(abstract: Elector::class)->resolveRouteBinding(value: $request->route(param: 'elector'));

                        if (
                            blank($elector) ||
                            !Kudvo::getElection()->preference->ballot_link_unique
                        ) {
                            return redirect(to: Index::getUrl());
                        }

                        Login::doLogin(elector: $elector, panel: Filament::getCurrentPanel(), request: $request);

                        return app(abstract: LoginResponse::class);
                    })
                    ->middleware(middleware: 'signed')
                    ->name(name: 'eul');
            })
            ->colors(colors: [
                'primary' => Color::Green,
            ])
            ->font(family: 'Poppins')
            ->viteTheme(theme: 'resources/css/filament/election/theme.css')
            ->navigation(builder: false)
            ->databaseNotifications(condition: false)
            ->breadcrumbs(condition: false)
            ->maxContentWidth(maxContentWidth: MaxWidth::FiveExtraLarge)
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): HtmlString => $this->getBrandLogo())
            ->spa()
            ->renderHook(
                name: PanelsRenderHook::HEAD_START,
                hook: fn (Request $request) => Kudvo::getElection()?->isPwaEnabled() ?
                    '<link rel="manifest" href="'.Filament::getCurrentPanel()->route(name: 'web-app-manifest').'">' :
                    null,
            )
            ->renderHook(
                name: PanelsRenderHook::FOOTER,
                hook: fn () => Blade::render(string: '<x-filament.nomination.footer />')
            );
    }

    protected function getBrandLogo(): HtmlString
    {
        $organisation = Kudvo::getOrganisation();
        $logoUrl = $organisation->getFilamentAvatarUrl();

        return new HtmlString(
            html: <<<HTML
<img
    alt="$organisation->name\'s logo"
    src="$logoUrl"
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
