<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Filament\User\Resources\SurveyResource;
use App\Models\Survey;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;

class ManageSurveys extends ManageRecords
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->successRedirectUrl(fn (Survey $record) => SurveyResource::getUrl('edit', [$record]))
                ->slideOver(),
        ];
    }
}
