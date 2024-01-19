<?php

namespace App\Filament\Election\Http\Middleware;

use App\Models\Elector;
use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as BaseMiddleware;

class AuthenticateSession extends BaseMiddleware
{
    /**
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if (! $request->hasSession() || ! $this->guard()->check()) {
            return $next($request);
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Elector $elector */
        $elector = $this->guard()->user();

        if (! $elector->authSession?->isCurrent(sessionId: $request->session()->getId(), guardName: Filament::getAuthGuard())) {
            $this->logout($request);
        }

        $elector->authSession?->touchLastActivity();

        return $next($request);
    }

    protected function guard(): Guard|StatefulGuard|Factory
    {
        return Filament::auth();
    }

    protected function redirectTo(Request $request): ?string
    {
        return Filament::getLoginUrl();
    }
}
