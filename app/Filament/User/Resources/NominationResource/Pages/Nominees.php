<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

class Nominees extends NominationPage
{
    protected static string $view = 'filament.resources.nomination-resource.pages.nominees';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    public static function getNavigationLabel(): string
    {
        return 'Nominees';
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation($parameters);
    }
}
