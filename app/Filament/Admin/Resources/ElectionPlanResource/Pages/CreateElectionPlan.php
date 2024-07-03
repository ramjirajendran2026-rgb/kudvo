<?php

namespace App\Filament\Admin\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateElectionPlan extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ElectionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    public function getSteps(): array
    {
        return self::$resource::getWizardSteps();
    }
}
