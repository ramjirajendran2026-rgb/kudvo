<?php

namespace App\Filament\Nomination\Pages\Concerns;

use App\Facades\Kudvo;
use App\Models\Elector;
use App\Models\Nomination;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

trait InteractsWithNomination
{
    #[Locked]
    protected Nomination $nomination;

    #[Locked]
    protected Elector $elector;

    public function bootInteractsWithNomination(): void
    {
        $this->nomination = Kudvo::getNomination();

        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        $this->elector = $elector;
    }

    public function getNomination(): Nomination
    {
        return $this->nomination;
    }

    public function getElector(): Elector
    {
        return $this->elector;
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parameters['nomination'] ??= Kudvo::getNomination();

        return route(static::getRouteName($panel), $parameters, $isAbsolute);
    }

    public function getHeading(): string|Htmlable
    {
        return $this->nomination->name;
    }
}
