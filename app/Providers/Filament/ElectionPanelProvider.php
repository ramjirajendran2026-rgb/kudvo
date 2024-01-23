<?php

namespace App\Providers\Filament;

use App\Facades\Kudvo;
use App\Filament\Election\Http\Middleware\AuthenticateSession;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Pages\Auth\Login;
use App\Filament\Election\Pages\Dashboard;
use App\Filament\ElectionPanel;
use App\Filament\Http\Middleware\IdentifyElection;
use App\Models\Organisation;
use Filament\Facades\Filament;
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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ElectionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return ElectionPanel::make()
            ->id(id: 'election')
            ->path(path: 'election/{election}')
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
            ->routes(routes: function () {
                Route::get(
                    uri: 'election.webmanifest',
                    action: function () {
                        $election = Kudvo::getElection();
                        $organisation = Kudvo::getOrganisation();

                        /** @var ElectionPanel $panel */
                        $panel = Filament::getCurrentPanel();

                        $data = [
                            'name' => $election->name,
                            'display' => 'standalone',
                            'start_url' => Dashboard::getUrl(),
                            'id' => $panel->getId().'/'.$election->getRouteKey(),
                            'theme_color' => '#0000ff',
                            'icons' => [],
                        ];

                        if ($organisation->logo_url) {
                            $data['icons'][] = [
                                'src' => $organisation->logo_url,
                                'type' => 'image/png',
                                'sizes' => '512x512',
                            ];
                        } else {
                            $data['icons'][] = [
                                'src' => Filament::getTenantAvatarUrl(tenant: $organisation).'&size=512',
                                'type' => 'image/png',
                                'sizes' => '512x512',
                            ];
                        }

                        return response()->json($data);
                    }
                )
                ->name(name: 'webmanifest');
            })
            ->colors(colors: [
                'primary' => Color::Amber,
            ])
            ->font(family: 'Poppins')
            ->viteTheme('resources/css/filament/election/theme.css')
            ->navigation(builder: false)
            ->databaseNotifications(condition: false)
            ->breadcrumbs(condition: false)
            ->maxContentWidth(maxContentWidth: MaxWidth::FiveExtraLarge)
            ->spa()
            ->brandName(name: fn (): string => Kudvo::getOrganisation()?->name)
            ->brandLogo(logo: fn (): string => Kudvo::getOrganisation()?->getFilamentAvatarUrl())
            ->renderHook(
                name: 'panels::head.start',
                hook: fn () => new HtmlString('<link rel="manifest" href="'.Filament::getCurrentPanel()->route(name: 'webmanifest').'">')
            )
            ->renderHook(
                name: 'panels::footer',
                hook: fn () => Blade::render('<x-filament.nomination.footer />')
            )
            ->renderHook(
                name: 'panels::footer',
                hook: fn () => new HtmlString(
                    html: <<<'HTML'
<script>
document.addEventListener('livewire:navigated', () => {
    if ('OTPCredential' in window) {
        console.log('listening to OTP');

        const ac = new AbortController();

        navigator.credentials.get({
            otp: { transport:['sms'] },
            signal: ac.signal
        }).then(otp => {
            $wire.dispatch('otp-received', { code: otp.code });

            document.dispatchEvent(new CustomEvent('otp-received', { code: otp.code }));
        }).catch(err => {
            console.log(err);
        });
    }
})
</script>
HTML

                )
            );
    }
}
