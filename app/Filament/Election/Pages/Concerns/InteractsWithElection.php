<?php

namespace App\Filament\Election\Pages\Concerns;

use App\Facades\Kudvo;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Locked;
use function Filament\authorize;

trait InteractsWithElection
{
    #[Locked]
    protected Election $election;

    #[Locked]
    protected Elector $elector;

    public function bootInteractsWithElection(): void
    {
        $this->election = Kudvo::getElection();

        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        $this->elector = $elector;
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parameters['election'] ??= Kudvo::getElection();

        return route(name: static::getRouteName($panel), parameters: $parameters, absolute: $isAbsolute);
    }

    public static function can(string $action): bool
    {
        try {
            return authorize(action: $action, model: Kudvo::getElection())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getElector(): Elector
    {
        return $this->elector;
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getElection()->name;
    }
}
