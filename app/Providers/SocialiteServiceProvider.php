<?php

namespace App\Providers;

use App\Services\Socialite\SocialiteManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class SocialiteServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new SocialiteManager($app);
        });
    }

    public function provides(): array
    {
        return [Factory::class];
    }
}
