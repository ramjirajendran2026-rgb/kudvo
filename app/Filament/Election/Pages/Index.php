<?php

namespace App\Filament\Election\Pages;

use App\Enums\ElectionPanelState;
use App\Facades\Kudvo;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Pages\Concerns\HasStateSection;
use App\Models\Elector;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;

class Index extends Page
{
    use HasStateSection;
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.index';

    protected static ?string $slug = '/';

    public ?ElectionPanelState $state = null;

    public bool $mock;

    public function mount(Request $request): void
    {
        $this->mock = $request->query(key: 'mock', default: false);

        $this->state = Kudvo::getElectionPanelState();

        if ($this->state == ElectionPanelState::Open) {
            $this->redirect(url: Dashboard::getUrl(), navigate: $this->isSpa());
        }
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return [
            EnsureStateIsAllowed::class,
            ...$panel->getAuthMiddleware()
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getElection()->name;
    }

    public function isSpa(): bool
    {
        return Filament::getCurrentPanel()->hasSpaMode();
    }

    public function isMock(): bool
    {
        return $this->mock;
    }

    public function getState(): ?ElectionPanelState
    {
        return $this->state;
    }

    public function getStateHeading(): string | Htmlable | null
    {
        return $this->getState()?->getLabel(election: $this->getElection());
    }

    public function getStateIcon(): ?string
    {
        return $this->getState()?->getIcon(election: $this->getElection());
    }

    public function getStateDescription(): string|Htmlable|null
    {
        return $this->getState()?->getDescription(election: $this->getElection(), elector: $this->getElector());
    }

    protected function getElector()
    {
        /** @var ?Elector $elector */
        $elector = Filament::auth()->user();

        return $elector;
    }
}
