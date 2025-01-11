<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Enums\MeetingOnboardingStep;
use App\Models\Meeting;
use Filament\Widgets\Widget;

class MeetingOnboardingWidget extends Widget
{
    protected static string $view = 'filament.user.resources.meeting-resource.widgets.meeting-onboarding-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public Meeting $record;

    public MeetingOnboardingStep $currentStep;

    public MeetingOnboardingStep $pendingStep;

    public function getSteps(): array
    {
        return MeetingOnboardingStep::sortedCases();
    }

    public function getStepUrl(MeetingOnboardingStep $step): ?string
    {
        return $step->getUrl(['record' => $this->record]);
    }
}
