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
use Illuminate\Support\HtmlString;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Locked;
use function Filament\authorize;

trait InteractsWithElector
{
    #[Locked]
    protected Elector $elector;

    public function bootInteractsWithElector(): void
    {
        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        $this->elector = $elector;
    }

    public function getElector(): Elector
    {
        return $this->elector;
    }
}
