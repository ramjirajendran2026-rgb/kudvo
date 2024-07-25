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
    public function __construct(protected Agent $agent) {}

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
            ! Kudvo::isBoothDevice() &&
            blank($elector) &&
            ! $election->preference->ballot_link_common => ElectionPanelState::CommonLinkRestricted,

            ! Kudvo::isBoothDevice() &&
            $election->preference->mfa_sms_auto_fill_only &&
            ! $this->agent->isiOS() &&
            ! $this->agent->isAndroidOS() => ElectionPanelState::DeviceNotSupported,

            $election->is_cancelled => ElectionPanelState::Cancelled,

            $elector?->ballot?->isVoted() => ElectionPanelState::Voted,

            $election->is_closed,
            ! Kudvo::isBoothDevice() && $election->is_expired,
            Kudvo::isBoothDevice() && $election->is_booth_expired => ElectionPanelState::Closed,

            ! Kudvo::isBoothDevice() && $election->is_upcoming,
            Kudvo::isBoothDevice() && $election->is_booth_upcoming => ElectionPanelState::YetToStart,

            ! Kudvo::isBoothDevice() && (
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

            ! Kudvo::isBoothDevice() && $election->is_open,
            Kudvo::isBoothDevice() && $election->is_booth_open => ElectionPanelState::Open,

            default => null,
        });

        return $next($request);
    }
}
