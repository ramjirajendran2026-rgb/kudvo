<?php

namespace App\Providers;

use App\Facades\Kudvo;
use App\KudvoManager;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(abstract: 'kudvo', concrete:  function (): KudvoManager {
            return new KudvoManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::isLocal()) {
            Mail::alwaysTo(address: 'iliyas.inode@gmail.com');
        }

        Str::macro(name: 'isUnicode', macro: fn ($string): bool => strlen($string) != strlen(utf8_decode($string)));
        Str::macro(name: 'maxLimit', macro: function ($value, $limit = 100, $end = '...'): string {
            if (mb_strwidth($value, 'UTF-8') <= $limit) {
                return $value;
            }

            return rtrim(mb_strimwidth(string: $value, start: 0, width: $limit, trim_marker: $end, encoding: 'UTF-8'));
        });

        Arr::macro(name: 'implodeWithAnd', macro: static function (array $array, string $separator = ', '): string {
            if (empty($array)) {
                return '';
            }

            if (count(value: $array) === 1) {
                return Arr::first($array);
            }

            $lastItem = array_pop($array);

            return implode(separator: $separator, array: $array).' and '.$lastItem;
        });

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
