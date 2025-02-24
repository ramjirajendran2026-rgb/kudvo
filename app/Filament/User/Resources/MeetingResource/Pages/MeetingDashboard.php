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
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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
            new HtmlString(Blade::render(
                <<<'BLADE'
<div class="flex flex-wrap items-center gap-x-2">
    <x-filament::badge
        :color="$status->getColor()"
        class="tracking-widest font-semibold"
    >
        {{ $status->getLabel() }}
    </x-filament::badge>
    <x-filament::badge
        icon="heroicon-m-clipboard-document"
        icon-position="after"
        class="font-mono tracking-widest cursor-pointer"
        x-on:click="
            window.navigator.clipboard.writeText('{{ $code }}')
            $tooltip('Copied', {
                theme: $store.theme,
                timeout: 2000,
            })
        "
    >
        {{ $code }}
    </x-filament::badge>
</div>
BLADE
                ,
                [
                    'code' => $this->getMeeting()->code,
                    'status' => $this->getMeeting()->status,
                    'votingStartsAt' => $this->getMeeting()->voting_starts_at_local->format(format: 'd M, Y h:i A (T)'),
                    'votingEndsAt' => $this->getMeeting()->voting_ends_at_local->format(format: 'd M, Y h:i A (T)'),
                ]
            )),
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
            $this->getPreviousPageAction(),

            $this->getEditAction(),

            ActionGroup::make(actions: [
                $this->getDeleteAction(),
                $this->getCloseVotingAction(),
                $this->getCancelAction(),
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
            MeetingResource\Widgets\VotersTurnoutChart::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        if ($this->hasPendingOnboardingStep()) {
            return [];
        }

        return [
            MeetingResource\Widgets\MeetingParticipantsWidget::make(),
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
            MeetingDashboardState::VotingInProgress, MeetingDashboardState::VotingEnded => [
                $this->getCloseVotingAction(),
                $this->getExtendVotingTimeAction(),
            ],
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

    public function getStateIcon(): ?string
    {
        return $this->state?->getIcon($this->getMeeting());
    }

    public function getStateDescription(): string | Htmlable | null
    {
        return $this->state?->getDescription($this->getMeeting());
    }

    public function getExtendVotingTimeAction(): Action
    {
        return MeetingResource::getExtendVotingTimeAction()
            ->authorize($this->canExtendVotingTime())
            ->after(callback: fn () => $this->dispatch('refresh')->self());
    }

    public function getCancelAction(): Action
    {
        return Action::make('cancelMeeting')
            ->requiresConfirmation()
            ->authorize($this->canCancel())
            ->action(function (Meeting $meeting, Action $action) {
                $meeting->touch('cancelled_at');

                $action->success();
            })
            ->after(callback: fn () => $this->dispatch('refresh')->self())
            ->color('danger')
            ->icon('heroicon-m-x-circle')
            ->successNotificationTitle('Cancelled successfully');
    }

    public function getCloseVotingAction(): Action
    {
        return Action::make('closeVoting')
            ->requiresConfirmation()
            ->authorize($this->canCloseVoting())
            ->action(function (Meeting $meeting, Action $action) {
                $meeting->touch('voting_closed_at');

                $action->success();
            })
            ->after(callback: fn () => $this->dispatch('refresh')->self())
            ->color('warning')
            ->icon('heroicon-m-lock-closed')
            ->successNotificationTitle('Voting closed successfully');
    }

    public function getDownloadResultAction(): Action
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

    public function getDownloadDetailedResultAction(): Action
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

    public function canCancel(): bool
    {
        return self::getResource()::can('cancel', $this->getMeeting());
    }

    public function canExtendVotingTime(): bool
    {
        return self::getResource()::can('extendVotingTime', $this->getMeeting());
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
