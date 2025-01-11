<?php

namespace App\Filament\User\Resources\MeetingResource\Pages\Concerns;

use App\Enums\MeetingOnboardingStep;
use App\Filament\Base\Pages\Concerns\InteractsWithFooterActions;
use App\Filament\User\Resources\MeetingResource\Widgets\MeetingOnboardingWidget;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Livewire\Attributes\On;

trait UsesMeetingOnboardingWidget
{
    use InteractsWithFooterActions;

    public ?MeetingOnboardingStep $pendingOnboardingStep = null;

    public ?MeetingOnboardingStep $currentOnboardingStep = null;

    public function mountUsesMeetingOnboardingWidget(): void
    {
        $this->refreshPendingOnboardingStep();

        $this->authorizeOnboardingAccess();
    }

    public function authorizeOnboardingAccess(): void
    {
        if (
            $this->hasPendingOnboardingStep() &&
            $this->getPendingOnboardingStep() !== $this->getCurrentOnboardingStep() &&
            $this->getPendingOnboardingStep()->getIndex() < $this->getCurrentOnboardingStep()->getIndex()
        ) {
            $this->redirect(url: $this->getPendingOnboardingStep()->getUrl(parameters: $this->getSubNavigationParameters()));
        }
    }

    public function getCurrentOnboardingStep(): ?MeetingOnboardingStep
    {
        return $this->currentOnboardingStep;
    }

    public function getPendingOnboardingStep(): ?MeetingOnboardingStep
    {
        return $this->pendingOnboardingStep;
    }

    public function getSubNavigation(): array
    {
        if ($this->hasPendingOnboardingStep()) {
            return [];
        }

        return parent::getSubNavigation();
    }

    public function hasPendingOnboardingStep(): bool
    {
        return filled($this->pendingOnboardingStep);
    }

    #[On('meeting.onboarding.refresh')]
    public function refreshPendingOnboardingStep(): void
    {
        $this->pendingOnboardingStep = $this->getRecord()->getOnboardingStep();
    }

    protected function getOnboardingWidgets(): array
    {
        if (! $this->hasPendingOnboardingStep() || blank($this->getCurrentOnboardingStep())) {
            return [];
        }

        return [
            MeetingOnboardingWidget::make(properties: [
                'currentStep' => $this->getCurrentOnboardingStep(),
                'pendingStep' => $this->getPendingOnboardingStep(),
            ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return $this->getOnboardingWidgets();
    }

    public function getNextStep(): ?MeetingOnboardingStep
    {
        if (! $this->hasPendingOnboardingStep() || blank($this->getCurrentOnboardingStep())) {
            return null;
        }

        return collect(MeetingOnboardingStep::cases())
            ->filter(fn (MeetingOnboardingStep $step) => $step->getIndex() > $this->getCurrentOnboardingStep()->getIndex())
            ->filter(fn (MeetingOnboardingStep $step) => $step->getIndex() <= $this->getPendingOnboardingStep()->getIndex())
            ->sortBy(fn (MeetingOnboardingStep $step) => $step->getIndex())
            ->first();
    }

    public function getPreviousStep(): ?MeetingOnboardingStep
    {
        if (! $this->hasPendingOnboardingStep() || blank($this->getCurrentOnboardingStep())) {
            return null;
        }

        return collect(MeetingOnboardingStep::cases())
            ->filter(fn (MeetingOnboardingStep $step) => $step->getIndex() < $this->getCurrentOnboardingStep()->getIndex())
            ->sortByDesc(fn (MeetingOnboardingStep $step) => $step->getIndex())
            ->first();
    }

    protected function getFooterActions(): array
    {
        return [
            $this->getPreviousPageAction(),

            $this->getNextPageAction(),
        ];
    }

    protected function getFooterActionsAlignment(): Alignment
    {
        return Alignment::Between;
    }

    protected function getNextPageAction(): Action
    {
        return Action::make(name: 'next')
            ->icon(icon: 'heroicon-o-arrow-right')
            ->url(url: fn (): ?string => $this->getNextStep()?->getUrl(parameters: $this->getSubNavigationParameters()))
            ->visible(condition: fn () => filled($this->getNextStep()));
    }

    protected function getPreviousPageAction(): Action
    {
        return Action::make(name: 'previous')
            ->color(color: 'gray')
            ->icon(icon: 'heroicon-o-arrow-left')
            ->url(url: fn (): ?string => $this->getPreviousStep()?->getUrl(parameters: $this->getSubNavigationParameters()))
            ->visible(condition: fn (): bool => filled($this->getPreviousStep()));
    }
}
