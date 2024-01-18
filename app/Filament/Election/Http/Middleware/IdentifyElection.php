<?php

namespace App\Filament\Election\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\ElectionPanel;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class IdentifyElection
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var ElectionPanel $panel */
        $panel = Filament::getCurrentPanel();

        abort_unless(boolean: $panel instanceof ElectionPanel, code: 404);

        Kudvo::setElection(
            election: $panel->getElection(
                key: $request->route()->parameter(name: 'election')
            )
        );

        return $next($request);
    }
}
