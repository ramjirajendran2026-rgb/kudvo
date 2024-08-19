<?php

namespace App\Filament\Admin\Resources\WikiCategoryResource\Pages;

use App\Filament\Admin\Resources\WikiCategoryResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;

class ManageWikiCategories extends ManageRecords
{
    protected static string $resource = WikiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::Medium),
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
