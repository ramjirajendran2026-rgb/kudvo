<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Columns\Column;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(callback: function (): void {
            Column::configureUsing(modifyUsing: function (Column $component) {
                $component->wrapHeader();
            });

            CreateAction::configureUsing(modifyUsing: function (CreateAction $action) {
                $action->icon(icon: 'heroicon-m-plus');
            });

            TableCreateAction::configureUsing(modifyUsing: function (TableCreateAction $action) {
                $action->icon(icon: 'heroicon-m-plus');
            });
        });
    }
}
