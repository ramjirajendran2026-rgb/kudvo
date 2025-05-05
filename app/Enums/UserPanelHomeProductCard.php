<?php

namespace App\Enums;

use App\Filament\User\Resources\ElectionResource;
use App\Filament\User\Resources\MeetingResource;
use App\Filament\User\Resources\NominationResource;
use App\Filament\User\Resources\SurveyResource;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserPanelHomeProductCard: string implements HasDescription, HasIcon, HasLabel
{
    case Election = 'election';
    case Meeting = 'meeting';
    case Nomination = 'nomination';
    case Survey = 'survey';

    public static function available(): array
    {
        return collect(self::cases())
            ->filter(fn (self $case) => $case->isAvailable())
            ->toArray();
    }

    public function isAvailable(): bool
    {
        return match ($this) {
            self::Election => ElectionResource::canAccess(),
            self::Nomination => NominationResource::canAccess(),
            self::Survey => SurveyResource::canAccess(),
            self::Meeting => MeetingResource::canAccess(),
        };
    }

    public function getBadgeLabel(): ?string
    {
        return match ($this) {
            self::Survey => 'Free',
            default => null,
        };
    }

    public function getBadgeColor(): ?string
    {
        return match ($this) {
            self::Survey => 'success',
            default => null,
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Election => 'A secure and efficient voting system for transparent elections.',
            self::Meeting => 'A scalable cloud solution for seamless meeting management with built-in resolution voting.',
            self::Nomination => 'A streamlined system for managing candidate nominations.',
            self::Survey => 'A simple, secure platform for creating and analyzing surveys.',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Election => ElectionResource::getNavigationIcon(),
            self::Meeting => MeetingResource::getNavigationIcon(),
            self::Nomination => NominationResource::getNavigationIcon(),
            self::Survey => SurveyResource::getNavigationIcon(),
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Election => ElectionResource::getNavigationLabel(),
            self::Meeting => MeetingResource::getNavigationLabel(),
            self::Nomination => NominationResource::getNavigationLabel(),
            self::Survey => SurveyResource::getNavigationLabel(),
        };
    }

    public function getUrl(): string
    {
        return match ($this) {
            self::Election => ElectionResource::getNavigationUrl(),
            self::Meeting => MeetingResource::getNavigationUrl(),
            self::Nomination => NominationResource::getNavigationUrl(),
            self::Survey => SurveyResource::getNavigationUrl(),
        };
    }
}
