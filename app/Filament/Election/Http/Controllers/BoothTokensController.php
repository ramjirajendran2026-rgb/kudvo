<?php

namespace App\Filament\Election\Http\Controllers;

use App\Facades\Kudvo;
use App\Filament\Election\Pages\Index;
use App\Http\Controllers\Controller;
use App\Models\ElectionBoothToken;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class BoothTokensController extends Controller
{
    public function activate(Agent $agent, ElectionBoothToken $boothToken)
    {
        abort_if(boolean: $agent->isRobot(), code: Response::HTTP_NOT_ACCEPTABLE);

        abort_if(
            boolean: $boothToken->isActivated() &&
                Kudvo::isBoothDevice() &&
                ! $boothToken->is(Kudvo::getElectionBoothToken()),
            code: Response::HTTP_UNAUTHORIZED
        );

        if (! $boothToken->isActivated()) {
            $boothToken->ip_address = request()->ip();
            $boothToken->user_agent = request()->userAgent();
            $boothToken->touch(attribute: 'activated_at');

            Cookie::queue(
                Cookie::forever(name: 'election_'.$boothToken->election->getKey().'_booth_token', value: $boothToken->key)
            );
        }

        return redirect()->to(Index::getUrl());
    }
}
