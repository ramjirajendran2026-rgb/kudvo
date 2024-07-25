<?php

namespace App\Filament\Admin\Resources\WikiPageResource\Pages;

use App\Filament\Admin\Resources\WikiPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWikiPage extends CreateRecord
{
    protected static string $resource = WikiPageResource::class;
}
