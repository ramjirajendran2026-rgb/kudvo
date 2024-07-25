<?php

namespace App\Listeners\Auth;

use App\Events\Auth\EmailVerified;
use Illuminate\Auth\Events\Verified;

class NotifyEmailVerified
{
    public function __construct() {}

    public function handle(Verified $event): void
    {
        EmailVerified::dispatch($event->user);
    }
}
