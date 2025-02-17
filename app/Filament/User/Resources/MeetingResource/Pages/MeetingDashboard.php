<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Actions\Meeting\GenerateDetailedResultPdf;
use App\Actions\Meeting\GenerateResultPdf;
use App\Enums\MeetingDashboardState;
use App\Enums\MeetingOnboardingStep;
use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\MeetingResource;
use App\Models\Meeting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Markdown;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;

class MeetingDashboard extends ViewRecord
{
    use Concerns\UsesMeetingOnboardingWidget;
    use HasStateSection;

    protected static string $resource = MeetingResource::class;

    protected static string $view = 'filament.user.resources.meeting-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public ?MeetingDashboardState $state = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->currentOnboardingStep = MeetingOnboardingStep::Publish;

        $this->refreshState();
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    public function getBreadcrumbs(): array
    {
        return [
            MeetingResource::getUrl() => MeetingResource::getBreadcrumb(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getSubheading(): string | Htmlable | null
    {
        return Markdown::inline(text: sprintf('**%s** to **%s**', $this->getMeeting()->voting_starts_at_local->format(format: 'M d, Y h:i A (T)'), $this->getMeeting()->voting_ends_at_local->format(format: 'M d, Y h:i A (T)')));
    }

    public function getMeeting(): Meeting
    {
        /** @var Meeting $meeting */
        $meeting = $this->getRecord();

        return $meeting;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getEditAction(),

            ActionGroup::make(actions: [
                $this->getDeleteAction(),
            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        if ($this->hasPendingOnboardingStep()) {
            return $this->getOnboardingWidgets();
        }

        return [
            MeetingResource\Widgets\MeetingStatsOverview::make(),
        ];
    }

    protected function getEditAction(): EditAction
    {
        return MeetingResource::getEditAction()
            ->iconButton();
    }

    protected function getDeleteAction(): DeleteAction
    {
        return MeetingResource::getDeleteAction();
    }

    protected function getPublishAction(): Action
    {
        return MeetingResource::getPublishAction()
            ->after(callback: fn () => $this->dispatch('refresh')->self());
    }

    protected function getStateActions(): array
    {
        return match ($this->state) {
            MeetingDashboardState::ReadyToPublish => [$this->getPublishAction()],
            MeetingDashboardState::VotingInProgress, MeetingDashboardState::VotingEnded => [$this->getCloseVotingAction()],
            MeetingDashboardState::VotingClosed => [
                $this->getDownloadResultAction(),
                $this->getDownloadDetailedResultAction(),
            ],
            default => [],
        };
    }

    #[On('refresh')]
    public function refreshState(): void
    {
        $this->refreshPendingOnboardingStep();

        $meeting = $this->getMeeting();

        $this->state = match (true) {
            $this->getPendingOnboardingStep() === MeetingOnboardingStep::AddParticipants => MeetingDashboardState::OnboardParticipants,
            $this->getPendingOnboardingStep() === MeetingOnboardingStep::AddResolutions => MeetingDashboardState::OnboardResolutions,
            $this->getPendingOnboardingStep() === MeetingOnboardingStep::Publish => MeetingDashboardState::ReadyToPublish,
            $meeting->isStatus(MeetingStatus::Cancelled) => MeetingDashboardState::Cancelled,
            //            $meeting->isStatus(MeetingStatus::Completed) => MeetingDashboardState::Completed,
            $meeting->isVotingStatus(MeetingVotingStatus::Scheduled) => MeetingDashboardState::VotingScheduled,
            $meeting->isVotingStatus(MeetingVotingStatus::Open) => MeetingDashboardState::VotingInProgress,
            $meeting->isVotingStatus(MeetingVotingStatus::Ended) => MeetingDashboardState::VotingEnded,
            $meeting->isVotingStatus(MeetingVotingStatus::Closed) => MeetingDashboardState::VotingClosed,
            default => null,
        };

        $this->cacheStateActions();
    }

    public function getStateHeading(): string | Htmlable | null
    {
        return $this->state?->getLabel($this->getMeeting());
    }

    public function getStateDescription(): string | Htmlable | null
    {
        return $this->state?->getDescription($this->getMeeting());
    }

    public function getCloseVotingAction()
    {
        return Action::make('closeVoting')
            ->requiresConfirmation()
            ->authorize($this->canCloseVoting())
            ->action(function (Meeting $meeting, Action $action) {
                $meeting->touch('voting_closed_at');

                $action->success();
            })
            ->after(callback: fn () => $this->dispatch('refresh')->self())
            ->successNotificationTitle('Voting closed successfully');
    }

    public function getDownloadResultAction()
    {
        return Action::make('downloadResult')
            ->authorize($this->canDownloadResult())
            ->action(function (Meeting $meeting) {
                $pdf = app(GenerateResultPdf::class)->execute($meeting);

                return response()->streamDownload(function () use ($pdf) {
                    echo base64_decode($pdf->base64());
                }, 'result-' . $meeting->getRouteKey() . '.pdf');
            })
            ->icon('heroicon-m-arrow-down-tray')
            ->label('Result');
    }

    public function getDownloadDetailedResultAction()
    {
        return Action::make('downloadDetailedResult')
            ->authorize($this->canDownloadDetailedResult())
            ->action(function (Meeting $meeting) {
                $pdf = app(GenerateDetailedResultPdf::class)->execute($meeting);

                return response()->streamDownload(function () use ($pdf) {
                    echo base64_decode($pdf->base64());
                }, 'detailed-result-' . $meeting->getRouteKey() . '.pdf');
            })
            ->icon('heroicon-m-arrow-down-tray')
            ->label('Detailed result');
    }

    public function canCloseVoting(): bool
    {
        return self::getResource()::can('closeVoting', $this->getMeeting());
    }

    public function canDownloadResult(): bool
    {
        return self::getResource()::can('downloadResult', $this->getMeeting());
    }

    public function canDownloadDetailedResult(): bool
    {
        return self::getResource()::can('downloadDetailedResult', $this->getMeeting());
    }
}
