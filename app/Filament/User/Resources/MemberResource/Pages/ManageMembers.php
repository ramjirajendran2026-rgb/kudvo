<?php

namespace App\Filament\User\Resources\MemberResource\Pages;

use App\Filament\User\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMembers extends ManageRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                MemberResource::getGenerateDummyMembersAction(),
            ])->dropdownPlacement('bottom-end'),

            MemberResource::getImportAction(),

            Actions\CreateAction::make(),
        ];
    }
}
