<?php

namespace App\Filament\Admin\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElectionPlan extends EditRecord
{
    use EditRecord\Concerns\HasWizard;
    use EditRecord\Concerns\Translatable;

    protected static string $resource = ElectionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
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
