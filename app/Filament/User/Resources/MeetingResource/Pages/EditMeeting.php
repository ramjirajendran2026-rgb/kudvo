<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Enums\MeetingOnboardingStep;
use App\Filament\User\Resources\MeetingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditMeeting extends EditRecord
{
    use Concerns\UsesMeetingOnboardingWidget;

    protected static string $resource = MeetingResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Between;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->currentOnboardingStep = MeetingOnboardingStep::CreateMeeting;
        $this->pendingOnboardingStep = $this->getRecord()->getOnboardingStep();
    }

    public function getCurrentOnboardingStep(): MeetingOnboardingStep
    {
        return MeetingOnboardingStep::CreateMeeting;
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),

            $this->getSaveFormAction(),
        ];
    }

    protected function getFooterActions(): array
    {
        return [];
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->alpineClickHandler(handler: null)
            ->url(
                url: fn (): string => $this->hasPendingOnboardingStep() ?
                ManageMeetings::getUrl() :
                MeetingDashboard::getUrl($this->getSubNavigationParameters())
            );
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getNextStep()?->getUrl(parameters: $this->getSubNavigationParameters()) ??
            MeetingDashboard::getUrl(parameters: $this->getSubNavigationParameters());
    }
}
