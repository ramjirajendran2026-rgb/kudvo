<?php

namespace App\Enums;

use App\Filament\User\Resources\MeetingResource;
use App\Filament\User\Resources\MeetingResource\Pages\EditMeeting;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingDashboard;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingParticipants;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingResolutions;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MeetingOnboardingStep: string implements HasDescription, HasIcon, HasLabel
{
    case CreateMeeting = 'create_meeting';
    case AddParticipants = 'add_participants';
    case AddResolutions = 'add_resolutions';
    // case MakePayment = 'make_payment';
    case Publish = 'publish';

    public static function sortedCases(): array
    {
        return collect(self::cases())->sortBy(fn ($case) => $case->getIndex())->all();
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CreateMeeting => MeetingResource::getNavigationIcon(),
            self::AddParticipants => MeetingParticipants::getNavigationIcon(),
            self::AddResolutions => MeetingResolutions::getNavigationIcon(),
            self::Publish => 'heroicon-o-rocket-launch',
        };
    }

    public function getIndex(): int
    {
        return match ($this) {
            self::CreateMeeting => 0,
            self::AddParticipants => 1,
            self::AddResolutions => 2,
            self::Publish => 3,
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CreateMeeting => 'Create Meeting',
            self::AddParticipants => 'Add Participants',
            self::AddResolutions => 'Add Resolutions',
            self::Publish => 'Publish',
        };
    }

    public function getUrl(array $parameters = []): ?string
    {
        return match ($this) {
            self::CreateMeeting => EditMeeting::getUrl(parameters: $parameters),
            self::AddParticipants => MeetingParticipants::getUrl(parameters: $parameters),
            self::AddResolutions => MeetingResolutions::getUrl(parameters: $parameters),
            default => MeetingDashboard::getUrl(parameters: $parameters),
        };
    }
}
