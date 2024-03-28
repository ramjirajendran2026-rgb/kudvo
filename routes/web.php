<?php

use App\Filament\Election\Pages\Auth\Login;
use App\Filament\Election\Pages\Ballot\Index as BallotPage;
use App\Filament\Election\Pages\Index as ElectionPanel;
use App\Http\Controllers\CheckoutController;
use App\Models\Election;
use App\Models\Elector;
use App\Services\Clicksend\Http\Controllers\WebhookController as ClicksendWebhookController;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Cashier;

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

Route::redirect(uri: '/', destination: 'https://securedvoting.com');

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
