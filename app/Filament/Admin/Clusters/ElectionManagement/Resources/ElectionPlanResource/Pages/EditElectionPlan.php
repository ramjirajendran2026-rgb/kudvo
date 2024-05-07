<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectionPlan extends EditRecord
{
    use EditRecord\Concerns\HasWizard;

    protected static string $resource = ElectionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getSteps(): array
    {
        return self::$resource::getWizardSteps();
    }
}
