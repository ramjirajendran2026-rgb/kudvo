<?php

namespace App\Filament\Election\Http\Middleware;

use App\Enums\ElectionPanelState;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\Index;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class EnsureStateIsAllowed
{
    public function __construct(protected Agent $agent)
    { }

    public function handle(Request $request, Closure $next)
    {
        if (
            Str::startsWith(haystack: $request->url(), needles: Filament::getLogoutUrl()) ||
            Kudvo::getElectionPanelState() == ElectionPanelState::Open
        ) {
            return $next($request);
        }

        return redirect(to: Index::getUrl());
    }
}
