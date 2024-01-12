<?php

namespace App\Filament\Nomination\Resources\NomineeResource\Pages;

use App\Facades\Kudvo;
use App\Filament\Contracts\HasElector;
use App\Filament\Contracts\HasNomination;
use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use App\Filament\Nomination\Resources\NomineeResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageNominees extends ManageRecords implements HasElector, HasNomination
{
    use InteractsWithNomination;

    protected static string $resource = NomineeResource::class;

    public function getHeading(): string|Htmlable
    {
        return Kudvo::getNomination()->name;
    }
}
