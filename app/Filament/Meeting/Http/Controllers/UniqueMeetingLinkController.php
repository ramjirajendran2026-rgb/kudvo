<?php

namespace App\Filament\Meeting\Http\Controllers;

use App\Filament\Meeting\Pages\Auth\Login;
use App\Models\Participant;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class UniqueMeetingLinkController
{
    public function __invoke(Request $request)
    {
        $participant = app(abstract: Participant::class)->resolveRouteBinding($request->route('participant'));

        $panel = Filament::getCurrentPanel();
        $user = $panel->auth()->user();

        if ($participant->is($user)) {
            return redirect()->to($panel->getUrl());
        }

        if (filled($user)) {
            Filament::auth()->logout();

            session()->invalidate();
            session()->regenerateToken();
        }

        Login::doLogin(participant: $participant, panel: Filament::getCurrentPanel(), request: $request);

        return redirect()->to($panel->getUrl());
    }
}
