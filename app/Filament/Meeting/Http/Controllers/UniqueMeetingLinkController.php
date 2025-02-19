<?php

namespace App\Filament\Meeting\Http\Controllers;

use App\Filament\Meeting\Pages\Auth\Login;
use App\Models\Participant;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\Request;

class UniqueMeetingLinkController
{
    public function __invoke(Request $request)
    {
        $participant = app(abstract: Participant::class)->resolveRouteBinding($request->route('participant'));

        $user = Filament::getCurrentPanel()->auth()->user();

        if ($participant->is($user)) {
            return app(abstract: LoginResponse::class);
        }

        if (filled($user)) {
            Filament::auth()->logout();

            session()->invalidate();
            session()->regenerateToken();
        }

        Login::doLogin(participant: $participant, panel: Filament::getCurrentPanel(), request: $request);

        return app(abstract: LoginResponse::class);
    }
}
