<?php

namespace App\Filament\Nomination\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\NominationPanel;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class IdentifyNomination
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var NominationPanel $panel */
        $panel = Filament::getCurrentPanel();

        abort_unless(boolean: $panel instanceof NominationPanel, code: 404);

        Kudvo::setNomination(
            nomination: $panel->getNomination(
                key: $request->route()->parameter(name: 'nomination')
            )
        );

        return $next($request);
    }
}
