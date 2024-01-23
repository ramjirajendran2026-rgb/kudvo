<?php

namespace App\Filament\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\Contracts\ResolvesElection;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyElection
{
    public function handle(Request $request, Closure $next): mixed
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel instanceof ResolvesElection) {
            abort(code: Response::HTTP_BAD_REQUEST);
        }

        Kudvo::setElection(election: $panel->resolveElection(key: $request->route()->parameter(name: 'election')));

        return $next($request);
    }
}
