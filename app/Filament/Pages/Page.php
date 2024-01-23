<?php

namespace App\Filament\Pages;

use App\Facades\Kudvo;
use App\Filament\Contracts\ResolvesElection;
use Filament\Facades\Filament;
use Filament\Pages\Page as BasePage;
use Illuminate\Database\Eloquent\Model;

class Page extends BasePage
{
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if (blank($panel) || Filament::getPanel(id: $panel) instanceof ResolvesElection) {
            $parameters['election'] ??= Kudvo::getElection();
        }

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
    }
}
