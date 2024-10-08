<?php

namespace App\Filament\Election\Http\Middleware;

use App\Facades\Kudvo;
use App\Models\Elector;
use Closure;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class IdentifyBoothToken
{
    public function handle(Request $request, Closure $next)
    {
        $election = Kudvo::getElection();
        $token = Cookie::get(key: 'election_' . Kudvo::getElection()->getKey() . '_booth_token');

        if (
            filled($token) &&
            filled($boothToken = $election->boothTokens()->firstWhere('key', $token))
        ) {
            Kudvo::setElectionBoothToken(token: $boothToken);

            /** @var Elector $authElector */
            $authElector = Filament::auth()->user();

            if ($authElector && $authElector->getKey() != $boothToken->current_elector_id) {
                Filament::auth()->logout();

                return app(LogoutResponse::class);
            }
        }

        return $next($request);
    }
}
