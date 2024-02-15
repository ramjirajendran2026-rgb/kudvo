<?php

use App\Filament\Election\Pages\Auth\Login;
use App\Filament\Election\Pages\Ballot\Index as BallotPage;
use App\Filament\Election\Pages\Index as ElectionPanel;
use App\Models\Election;
use App\Models\Elector;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
