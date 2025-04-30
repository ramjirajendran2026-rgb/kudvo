<?php

namespace App\Filament\User\Resources\BranchResource\Pages;

use App\Filament\User\Resources\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBranches extends ManageRecords
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalCancelAction(false)
                ->successRedirectUrl(BranchResource::getUrl()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BranchResource\Widgets\Branches::make(),
        ];
    }
}
