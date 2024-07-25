<?php

namespace App\Enums;

use App\Filament\User\Resources\ElectionResource\Pages\BallotSetup;
use App\Filament\User\Resources\ElectionResource\Pages\Dashboard;
use App\Filament\User\Resources\ElectionResource\Pages\Electors;
use App\Filament\User\Resources\ElectionResource\Pages\Preference;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ElectionSetupStep: string implements HasDescription, HasIcon, HasLabel
{
    case Preference = 'preference';
    case Electors = 'electors';
    case Ballot = 'ballot';
    case Timing = 'timing';
    case Payment = 'payment';
    case Publish = 'publish';

    public function getLabel(): ?string
    {
        return __(key: 'app.enums.election_setup_step.' . $this->value . '.label');
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getIndex(): int
    {
        return match ($this) {
            self::Preference => 1,
            self::Electors => 2,
            self::Ballot => 3,
            self::Timing => 4,
            self::Payment => 5,
            self::Publish => 6,
        };
    }

    public function getIcon(): ?string
    {
        return null;
    }

    public function getUrl(array $parameters = []): ?string
    {
        return match ($this) {
            self::Preference => Preference::getUrl($parameters),
            self::Electors => Electors::getUrl($parameters),
            self::Ballot => BallotSetup::getUrl($parameters),
            self::Timing,
            self::Payment,
            self::Publish => Dashboard::getUrl($parameters),
        };
    }
}
