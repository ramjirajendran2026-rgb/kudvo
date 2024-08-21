<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EnableHsts
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (App::isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains;');
        }

        return $response;
    }
}
