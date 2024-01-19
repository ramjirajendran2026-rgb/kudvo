<?php

namespace App\Listeners;

use App\Models\Elector;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Auth\Events\Logout;

class DestroyBrowserSession
{
    public function handle(Logout|CurrentDeviceLogout $event): void
    {
        $user = $event->user;

        if (! $user instanceof Elector) {
            return;
        }

        $user->authSession?->delete();
    }
}
