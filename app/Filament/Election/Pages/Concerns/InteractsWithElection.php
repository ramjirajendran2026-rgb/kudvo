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

trait InteractsWithElection
{
    #[Locked]
    protected Election $election;

    public function bootInteractsWithElection(): void
    {
        $this->election = Kudvo::getElection();
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

    public function getWidgetData(): array
    {
        return array_merge(
            parent::getWidgetData(),

            ['election' => $this->getElection()],
        );
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getElection()->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! $this->getElection()->isTimingConfigured()) {
            return null;
        }

        return new HtmlString(
            html: <<<HTML
<div class="flex justify-center items-center gap-4">
<div class="flex flex-col md:flex-row flex-grow justify-center items-end md:gap-2 font-bold">
<span>{$this->getElection()->starts_at_local->format(format: 'M d, Y')}</span>
<span>{$this->getElection()->starts_at_local->format(format: 'h:i A')}</span>
<span>{$this->getElection()->starts_at_local->format(format: '(T)')}</span>
</div>
<span>to</span>
<div class="flex flex-col md:flex-row flex-grow justify-center items-start md:gap-2 font-bold">
<span>{$this->getElection()->ends_at_local->format(format: 'M d, Y')}</span>
<span>{$this->getElection()->ends_at_local->format(format: 'h:i A')}</span>
<span>{$this->getElection()->ends_at_local->format(format: '(T)')}</span>
</div>
</div>
HTML
        );
    }
}
