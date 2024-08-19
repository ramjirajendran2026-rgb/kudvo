<?php

namespace App\Filament\Base\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;

trait HasStateSection
{
    protected array $cachedStateActions = [];

    public function bootedHasStateSection(): void
    {
        $this->cacheStateActions();
    }

    protected function cacheStateActions(): void
    {
        /** @var array<string, Action | ActionGroup> $actions */
        $actions = Action::configureUsing(
            $this->configureAction(...),
            fn (): array => $this->getStateActions(),
        );

        $this->cachedStateActions = array_map(function ($action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);
                $this->mergeCachedActions($action->getFlatActions());
            } elseif (! $action instanceof Action) {
                throw new InvalidArgumentException('State actions must be an instance of ' . Action::class . ', or ' . ActionGroup::class . '.');
            }

            $this->cacheAction($action);

            return $action;
        }, $actions);
    }

    public function getCachedStateActions(): array
    {
        return $this->cachedStateActions;
    }

    protected function getStateActions(): array
    {
        return [];
    }

    public function getStateDescription(): string | Htmlable | null
    {
        return null;
    }

    public function getStateHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getStateIcon(): ?string
    {
        return null;
    }
}
