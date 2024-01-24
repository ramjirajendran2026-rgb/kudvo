<?php

namespace App\Filament\Ballot\Pages;

use App\Facades\Kudvo;
use App\Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Index extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.ballot.pages.index';

    protected static ?string $slug = '/';

    protected static ?string $title = 'Ballot';

    public function getHeading(): string|Htmlable
    {
        return Kudvo::getElection()->name;
    }
}
