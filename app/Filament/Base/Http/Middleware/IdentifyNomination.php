<?php

namespace App\Filament\Base\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\Base\Contracts\ResolvesNomination;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyNomination
{
    public function handle(Request $request, Closure $next): mixed
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel instanceof ResolvesNomination) {
            abort(code: Response::HTTP_BAD_REQUEST);
        }

        Kudvo::setNomination(nomination: $panel->resolveNomination(key: $request->route()->parameter(name: 'nomination')));

        return $next($request);
    }
}
