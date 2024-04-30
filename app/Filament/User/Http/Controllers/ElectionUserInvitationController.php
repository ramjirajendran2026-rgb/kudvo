<?php

namespace App\Filament\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ElectionUserInvitation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ElectionUserInvitationController extends Controller
{
    public function accept(Request $request, ElectionUserInvitation $invitation)
    {
        // Accept the invitation
        abort_unless(boolean: $request->user()->email === $invitation->email, code: Response::HTTP_UNAUTHORIZED);

        $invitation->update([
            'accepted_at' => now(),
            'user_id' => $request->user()->id,
        ]);

        $invitation->election->collaborators()->attach(
            $invitation->user,
            [
                'designation' => $invitation->designation,
                'permissions' => $invitation->permissions,
            ]
        );

        return redirect()->route(
            route: 'filament.user.resources.elections.dashboard',
            parameters: ['record' => $invitation->election, 'tenant' => $invitation->election->organisation]
        );
    }
}
