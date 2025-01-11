<?php

namespace App\Filament\Base\Pages\Concerns;

use Blade;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Support\Enums\Alignment;
use Filament\View\PanelsRenderHook;
use InvalidArgumentException;

trait InteractsWithFooterActions
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $cachedFooterActions = [];

    public function bootedInteractsWithFooterActions(): void
    {
        $this->cacheFooterActions();

        Filament::registerRenderHook(
            name: PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER,
            hook: fn () => Blade::render(
                string: <<<'BLADE'
<x-filament::actions :actions="$actions" :alignment="$footerActionsAlignment" />
BLADE
                ,
                data: [
                    'actions' => $this->getCachedFooterActions(),
                    'footerActionsAlignment' => $this->getFooterActionsAlignment(),
                ]
            )
        );
    }

    protected function cacheFooterActions(): void
    {
        /** @var array<string, Action | ActionGroup> */
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getFooterActions(),
        );

        foreach ($actions as $action) {
            if ($action instanceof ActionGroup) {
                $action->livewire($this);

                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedActions($flatActions);
                $this->cachedFooterActions[] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Footer actions must be an instance of ' . Action::class . ', or ' . ActionGroup::class . '.');
            }

            $this->cacheAction($action);
            $this->cachedFooterActions[] = $action;
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getCachedFooterActions(): array
    {
        return $this->cachedFooterActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFooterActions(): array
    {
        return [];
    }

    protected function getFooterActionsAlignment(): Alignment
    {
        return Alignment::End;
    }
}
