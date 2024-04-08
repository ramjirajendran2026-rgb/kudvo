<?php

namespace App\Filament\Admin\Resources\WikiPageResource\Pages;

use App\Filament\Admin\Resources\WikiPageResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWikiPages extends ListRecords
{
    protected static string $resource = WikiPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(label: 'Active'),
            'trashed' => Tab::make(label: 'Trashed')
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
