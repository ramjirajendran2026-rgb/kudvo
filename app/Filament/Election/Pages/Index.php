<?php

namespace App\Filament\Election\Pages;

use App\Enums\ElectionPanelState;
use App\Filament\Pages\Concerns\HasStateSection;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;

class Index extends BasePage
{
    use HasStateSection;

    protected static string $view = 'filament.election.pages.index';

    protected static ?string $slug = '/';

    public ?ElectionPanelState $state = null;

    protected static bool $isDiscovered = false;

    public function mount(Request $request): void
    {
        parent::mount($request);

        $this->resolveState();

        if ($this->state == ElectionPanelState::Open) {
            $this->redirect(url: Dashboard::getUrl(), navigate: $this->isSpa());
        }
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return $panel->getAuthMiddleware();
    }

    protected function resolveState(): void
    {
        $election = $this->getElection();

        $this->state = match (true) {
            $election->is_upcoming => ElectionPanelState::YetToStart,
            $election->is_closed => ElectionPanelState::Closed,
            $election->is_expired => ElectionPanelState::Ended,
            $election->is_open => ElectionPanelState::Open,
            default => null,
        };
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
        return $this->getState()?->getDescription(election: $this->getElection());
    }
}
