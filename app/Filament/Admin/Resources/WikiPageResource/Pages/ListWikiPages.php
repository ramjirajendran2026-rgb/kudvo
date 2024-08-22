<?php

namespace App\Filament\Admin\Resources\WikiPageResource\Pages;

use App\Actions\GenerateSitemap;
use App\Filament\Admin\Resources\WikiPageResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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

            ActionGroup::make([
                Action::make('generateSitemap')
                    ->action(function (Action $action): void {
                        GenerateSitemap::execute();

                        $action->success();
                    })
                    ->label('Generate Sitemap')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Generated successfully'),
            ]),
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
