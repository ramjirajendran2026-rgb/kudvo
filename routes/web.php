<?php

use App\Filament\Election\Pages\Index as ElectionPanel;
use App\Http\Controllers\AwsSnsController;
use App\Http\Controllers\CheckoutController;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\PrivacyPolicy;
use App\Livewire\Pages\Products\Election\Home as ElectionHome;
use App\Livewire\Pages\Products\Election\HowItWorks;
use App\Models\Election;
use App\Models\Elector;
use App\Services\Clicksend\Http\Controllers\WebhookController as ClicksendWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix(LaravelLocalization::setLocale())
    ->middleware([
        LocaleSessionRedirect::class,
        LaravelLocalizationRedirectFilter::class,
        LaravelLocalizationViewPath::class,
    ])
    ->group(function () {
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('livewire/update', $handle);
        });

        Route::get(uri: '/', action: Home::class)
            ->name(name: 'home');

        Route::prefix('products/election')
            ->name('products.election.')
            ->group(function (): void {
                Route::get(uri: '/', action: ElectionHome::class)
                    ->name(name: 'home');

                Route::get(uri: 'how-it-works', action: HowItWorks::class)
                    ->name(name: 'how-it-works');
            });
    });

Route::get(uri: 'privacy-policy', action: PrivacyPolicy::class)
    ->name(name: 'privacy-policy');

Route::get(
    uri: 'e/{election:short_code}',
    action: fn (Election $election) => redirect(to: ElectionPanel::getUrl(parameters: ['election' => $election], panel: 'election'))
)->name(name: 'short_link.election');

Route::get(
    uri: 'b/{elector:short_code}',
    action: fn (Request $request, Elector $elector) => redirect(to: URL::signedRoute(name: 'filament.election.eul', parameters: ['election' => $elector->event, 'elector' => $elector]))
)->name(name: 'short_link.ballot');

Route::post(uri: 'clicksend/webhook', action: ClicksendWebhookController::class);

Route::get(uri: 'checkout/success', action: [CheckoutController::class, 'success'])
    ->name(name: 'checkout.success');

Route::get(uri: 'checkout/cancel', action: [CheckoutController::class, 'cancel'])
    ->name(name: 'checkout.cancel');

foreach (AwsSnsController::$routes as $routeKey => $route) {
    $routeName = 'ses.notification.'.$routeKey;

    $controllerActionName = Str::camel($routeKey);

    Route::post('ses/notification/'.$route, [AwsSnsController::class, $controllerActionName])->name($routeName);
}
