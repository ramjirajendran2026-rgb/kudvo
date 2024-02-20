<?php

namespace App\Filament\Election\Http\Middleware;

use App\Facades\Kudvo;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class IdentifyBooth
{
    public function handle(Request $request, Closure $next)
    {
        $token = Cookie::get(key: 'election_'.Kudvo::getElection()->getKey().'_booth_token');

        if(
            filled($token) &&
            filled($boothToken = Kudvo::getElection()->boothTokens()->firstWhere('key', $token))
        ) {
            Kudvo::setElectionBoothToken(token: $boothToken);
        }

        return $next($request);
    }
}
