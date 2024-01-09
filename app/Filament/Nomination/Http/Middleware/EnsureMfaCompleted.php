<?php

namespace App\Filament\Nomination\Http\Middleware;

use App\Facades\Kudvo;
use App\Filament\Nomination\Pages\Mfa\Notice;
use App\Models\Elector;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EnsureMfaCompleted
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        if (
            ! Str::startsWith(haystack: $request->url(), needles: Filament::getLogoutUrl()) &&
            ! Session::has(key: Notice::getMfaCompletedSessionKey(elector: $elector))
        ) {
            return Redirect::guest(path: Notice::getUrl());
        }

        return $next($request);
    }
}
