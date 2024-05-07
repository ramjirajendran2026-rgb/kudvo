<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateElectionPlan extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ElectionPlanResource::class;

    public function getSteps(): array
    {
        return self::$resource::getWizardSteps();
    }
}
