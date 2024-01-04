<?php

namespace App\Filament\User\Resources\NominationResource\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use InvalidArgumentException;

trait InteractsWithState
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $cachedStateActions = [];

    public function bootedInteractsWithState(): void
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

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);
                $this->cachedStateActions[] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('State actions must be an instance of ' . Action::class . ', or ' . ActionGroup::class . '.');
            }

            $this->cacheAction($action);
            $this->cachedStateActions[] = $action;
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getCachedStateActions(): array
    {
        return $this->cachedStateActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getStateActions(): array
    {
        return [];
    }

    public function getStateDescription(): ?string
    {
        return null;
    }

    public function getStateHeading(): ?string
    {
        return null;
    }

    public function getStateIcon(): ?string
    {
        return null;
    }
}
