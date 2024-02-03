<?php

namespace App\Filament\Election\Http\Middleware;

use App\Enums\BallotType;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\DeviceAlreadyUsed;
use App\Filament\Election\Pages\DeviceNotSupported;
use App\Filament\Election\Pages\Mfa\Notice;
use App\Models\Ballot;
use App\Models\Elector;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class EnsureDeviceIsAllowed
{
    public function __construct(protected Agent $agent)
    { }

    public function handle(Request $request, Closure $next)
    {
        if (Str::startsWith(haystack: $request->url(), needles: Filament::getLogoutUrl())) {
            return $next($request);
        }

        if (! Kudvo::isBoothDevice() && ! $this->agent->isiOS() && ! $this->agent->isAndroidOS()) {
            return redirect(to: DeviceNotSupported::getUrl());
        }

        $election = Kudvo::getElection();
        $preference = $election->preference;

        if (! Kudvo::isBoothDevice() && Cookie::has(key: 'election_'.Kudvo::getElection()->getKey().'_ballot')) {
            return redirect(to: DeviceAlreadyUsed::getUrl());
        }

        if (
            ! Kudvo::isBoothDevice() &&
            $preference->ip_restriction_threshold &&
            Ballot::query()
                ->where('type', BallotType::Direct->value)
                ->where('ip_address', $request->ip())
                ->whereHas(
                    relation: 'elector',
                    callback: fn (Builder $query): Builder => $query
                        ->whereMorphedTo(relation: 'event', model: $election)
                )
                ->count() >= $preference->ip_restriction_threshold
        ) {
            return redirect(to: DeviceAlreadyUsed::getUrl());
        }

        return $next($request);
    }
}
