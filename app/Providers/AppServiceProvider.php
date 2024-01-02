<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
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

            Action::configureUsing(modifyUsing: function (Action $action) {
                $action->size(size: ActionSize::Small);
            });
        });
    }
}
