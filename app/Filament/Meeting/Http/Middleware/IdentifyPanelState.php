<?php

namespace App\Filament\Meeting\Http\Middleware;

use App\Actions\Meeting\DetectMeetingPanelState;
use App\Enums\MeetingStatus;
use App\Facades\Kudvo;
use App\Models\Participant;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyPanelState
{
    public function handle(Request $request, Closure $next)
    {
        $meeting = Kudvo::getMeeting();

        if (blank($meeting)) {
            return $next($request);
        }

        abort_if(boolean: $meeting->isStatus(MeetingStatus::Onboarding), code: Response::HTTP_NOT_FOUND);

        /** @var ?Participant $participant */
        $participant = Filament::auth()->user();

        app(DetectMeetingPanelState::class)->execute($meeting, $participant);

        return $next($request);
    }
}
