<?php

namespace App\Filament\Election\Http\Middleware;

use App\Facades\Kudvo;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class IdentifyBoothToken
{
    public function handle(Request $request, Closure $next)
    {
        $election = Kudvo::getElection();
        $token = Cookie::get(key: 'election_'.Kudvo::getElection()->getKey().'_booth_token');

        if(
            filled($token) &&
            filled($boothToken = $election->boothTokens()->firstWhere('key', $token)) &&
            ($election->is_booth_upcoming || $election->is_booth_open)
        ) {
            Kudvo::setElectionBoothToken(token: $boothToken);
        }

        return $next($request);
    }
}
