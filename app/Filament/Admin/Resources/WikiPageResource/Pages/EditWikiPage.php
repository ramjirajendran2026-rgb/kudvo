<?php

namespace App\Filament\Admin\Resources\WikiPageResource\Pages;

use App\Filament\Admin\Resources\WikiPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWikiPage extends EditRecord
{
    protected static string $resource = WikiPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
