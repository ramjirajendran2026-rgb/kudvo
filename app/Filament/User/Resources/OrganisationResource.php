<?php

namespace App\Filament\User\Resources;

use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;

class OrganisationResource extends Resource
{
    use Translatable;

    protected static bool $shouldRegisterNavigation = false;
}
