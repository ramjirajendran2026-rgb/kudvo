<?php

namespace App\Filament\Election\Http\Middleware;

use App\Enums\BallotType;
use App\Enums\ElectionPanelState;
use App\Facades\Kudvo;
use App\Models\Ballot;
use App\Models\Elector;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class IdentifyPanelState
{
    public function __construct(protected Agent $agent)
    { }

    public function handle(Request $request, Closure $next)
    {
        $election = Kudvo::getElection();

        if (blank($election)) {
            return $next($request);
        }

        abort_if(boolean: $election->is_draft, code: Response::HTTP_NOT_FOUND);

        /** @var ?Elector $elector */
        $elector = Filament::auth()->user();

        Kudvo::setElectionPanelState(state: match (true) {
            blank($elector) &&
            !$election->preference->ballot_link_common => ElectionPanelState::CommonLinkRestricted,

            !Kudvo::isBoothDevice(election: $election) &&
            $election->preference->mfa_sms_auto_fill_only &&
            !$this->agent->isiOS() &&
            !$this->agent->isAndroidOS() => ElectionPanelState::DeviceNotSupported,

            $election->is_cancelled => ElectionPanelState::Cancelled,

            $election->is_upcoming => ElectionPanelState::YetToStart,

            $election->is_closed,
            $election->is_expired => ElectionPanelState::Closed,

            $elector?->ballot?->isVoted() => ElectionPanelState::Voted,

            !Kudvo::isBoothDevice(election: $election) && (
                (
                    $election->preference->prevent_duplicate_device &&
                    filled(Cookie::get(key: "election_{$election->getKey()}_ballot"))
                ) ||
                (
                    $election->preference->ip_restriction_threshold &&
                    Ballot::query()
                        ->where('type', BallotType::Direct->value)
                        ->where('ip_address', $request->ip())
                        ->whereHas(
                            relation: 'elector',
                            callback: fn (Builder $query): Builder => $query
                                ->whereMorphedTo(relation: 'event', model: $election)
                        )
                        ->count() >= $election->preference->ip_restriction_threshold
                )
            ) => ElectionPanelState::DeviceAlreadyUsed,

            $election->is_open => ElectionPanelState::Open,

            default => null,
        });

        return $next($request);
    }
}
