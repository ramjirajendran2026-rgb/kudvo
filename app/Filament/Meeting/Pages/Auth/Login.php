<?php

namespace App\Filament\Meeting\Pages\Auth;

use App\Models\Participant;
use Filament\Pages\Auth\Login as BasePage;
use Filament\Panel;
use Illuminate\Http\Request;

class Login extends BasePage
{
    public static function doLogin(Participant $participant, Panel $panel, Request $request): void
    {
        $panel->auth()->login(user: $participant);
        session()->regenerate();

        $participant->createAuthSession(
            sessionId: session()->getId(),
            guardName: $panel->getAuthGuard(),
            request: $request,
        );
    }
}
