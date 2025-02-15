<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Filament\User\Resources\SurveyResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;

class CreateSurvey extends CreateRecord
{
    protected static string $resource = SurveyResource::class;

    protected static bool $canCreateAnother = false;

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenMedium;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
