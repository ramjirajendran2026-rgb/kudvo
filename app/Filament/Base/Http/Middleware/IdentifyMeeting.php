<?php

namespace App\Filament\Base\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\Base\Contracts\ResolvesMeeting;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyMeeting
{
    public function handle(Request $request, Closure $next): mixed
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel instanceof ResolvesMeeting) {
            abort(code: Response::HTTP_BAD_REQUEST);
        }

        Kudvo::setMeeting(meeting: $panel->resolveMeeting(key: $request->route(param: 'meeting')));

        return $next($request);
    }
}
