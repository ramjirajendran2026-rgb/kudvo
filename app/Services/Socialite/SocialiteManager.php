<?php

namespace App\Services\Socialite;

use App\Settings\ServiceConfig;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\LinkedInProvider;
use Laravel\Socialite\Two\XProvider;

class SocialiteManager extends \Laravel\Socialite\SocialiteManager
{
    protected function createFacebookDriver(): AbstractProvider
    {
        $config = collect(app(ServiceConfig::class)->facebook->toArray())
            ->only(['client_id', 'client_secret'])
            ->put('redirect', url('/login/facebook/redirect'))
            ->toArray();

        return $this->buildProvider(FacebookProvider::class, $config);
    }

    protected function createGoogleDriver(): AbstractProvider
    {
        $config = collect(app(ServiceConfig::class)->google->toArray())
            ->only(['client_id', 'client_secret'])
            ->put('redirect', url('/login/google/redirect'))
            ->toArray();

        return $this->buildProvider(GoogleProvider::class, $config);
    }

    protected function createGithubDriver(): AbstractProvider
    {
        $config = collect(app(ServiceConfig::class)->github->toArray())
            ->only(['client_id', 'client_secret'])
            ->put('redirect', url('/login/github/redirect'))
            ->toArray();

        return $this->buildProvider(GithubProvider::class, $config);
    }

    protected function createLinkedinDriver(): AbstractProvider
    {
        $config = collect(app(ServiceConfig::class)->linkedin->toArray())
            ->only(['client_id', 'client_secret'])
            ->put('redirect', url('/login/linkedin/redirect'))
            ->toArray();

        return $this->buildProvider(LinkedInProvider::class, $config);
    }

    protected function createXDriver(): AbstractProvider
    {
        $config = collect(app(ServiceConfig::class)->x->toArray())
            ->only(['client_id', 'client_secret'])
            ->put('redirect', url('/login/x/redirect'))
            ->toArray();

        return $this->buildProvider(XProvider::class, $config);
    }
}
