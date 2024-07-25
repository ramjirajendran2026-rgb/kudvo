<?php

namespace App\Filament\Election\Http\Controllers;

use App\Events\Election\Booth\Activated;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\Index;
use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionBoothToken;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class BoothTokensController extends Controller
{
    public function activate(Agent $agent, Election $election, ElectionBoothToken $token)
    {
        abort_if(boolean: $agent->isRobot(), code: Response::HTTP_NOT_ACCEPTABLE);

        abort_if(
            boolean: $token->isActivated() &&
                Kudvo::isBoothDevice() &&
                ! $token->is(Kudvo::getElectionBoothToken()),
            code: Response::HTTP_UNAUTHORIZED
        );

        if (! $token->isActivated()) {
            $token->ip_address = request()->ip();
            $token->user_agent = request()->userAgent();
            $token->touch(attribute: 'activated_at');

            Cookie::queue(
                Cookie::forever(name: 'election_' . $token->election->getKey() . '_booth_token', value: $token->key)
            );
        }

        broadcast(new Activated(boothId: $token->getKey()));

        return redirect()->to(Index::getUrl());
    }
}
