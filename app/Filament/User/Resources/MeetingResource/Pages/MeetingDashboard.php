<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Actions\Meeting\GenerateDetailedResultPdf;
use App\Enums\MeetingOnboardingStep;
use App\Filament\Base\Pages\Concerns\HasStateSection;
use App\Filament\User\Resources\MeetingResource;
use App\Models\Meeting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class MeetingDashboard extends ViewRecord
{
    use Concerns\UsesMeetingOnboardingWidget;
    use HasStateSection;

    protected static string $resource = MeetingResource::class;

    protected static string $view = 'filament.user.resources.meeting-resource.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->currentOnboardingStep = MeetingOnboardingStep::Publish;
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

    protected function getHeaderActions(): array
    {
        return [
            $this->getEditAction(),

            ActionGroup::make(actions: [
                $this->getDeleteAction(),

                $this->getDownloadDetailedResultAction(),
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

    protected function getStateActions(): array
    {
        if ($this->getPendingOnboardingStep() === MeetingOnboardingStep::Publish) {
            return [
                MeetingResource::getPublishAction(),
            ];
        }

        return [];
    }

    public function getStateHeading(): string | Htmlable | null
    {
        if ($this->getPendingOnboardingStep() === MeetingOnboardingStep::Publish) {
            return 'Publish Meeting';
        }

        return null;
    }

    public function getDownloadDetailedResultAction()
    {
        return Action::make('downloadDetailedResult')
            ->authorize('downloadDetailedResult')
            ->action(function (Meeting $meeting) {
                $pdf = app(GenerateDetailedResultPdf::class)->execute($meeting);

                return response()->streamDownload(function () use ($pdf) {
                    echo base64_decode($pdf->base64());
                }, $meeting->getKey() . '.pdf');
            });
    }
}
