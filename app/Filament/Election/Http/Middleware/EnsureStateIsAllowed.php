<?php

namespace App\Filament\Election\Http\Middleware;

use App\Enums\ElectionPanelState;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\Ballot\Index as Ballot;
use App\Filament\Election\Pages\Index;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class EnsureStateIsAllowed
{
    public function __construct(protected Agent $agent) {}

    public function handle(Request $request, Closure $next)
    {
        if (
            (Kudvo::getElectionPanelState() == ElectionPanelState::Open) ||
            $request->routeIs('filament.election.auth.logout') ||
            (
                $request->routeIs('filament.election.eul') &&
                Kudvo::getElectionPanelState() == ElectionPanelState::CommonLinkRestricted
            ) ||
            (
                $request->routeIs('filament.election.pages.ballot') &&
                Ballot::canAccess()
            ) ||
            (
                $request->routeIs('filament.election.pages.mfa.*') &&
                Ballot::canAccess()
            )
        ) {
            return $next($request);
        }

        return redirect(to: Index::getUrl());
    }
}
