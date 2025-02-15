<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Home extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.user.pages.home';

    protected static ?string $title = 'Home';

    public array $products = [];

    public function mount(): void
    {
        $this->products = [
            [
                'id' => 1,
                'title' => 'Election',
                'description' => 'Complete IDE and debugging toolkit for professional developers',
                'icon' => 'heroicon-o-archive-box',
                'url' => route('products.election.home') . '#election-setup',
            ],
            [
                'id' => 2,
                'title' => 'Meeting',
                'description' => 'Secure and scalable cloud storage solution for enterprises',
                'icon' => 'heroicon-o-calendar',
                'url' => route('products.election.home') . '#election-setup',
            ],
            [
                'id' => 3,
                'title' => 'Nomination',
                'description' => 'Complete IDE and debugging toolkit for professional developers',
                'icon' => 'heroicon-o-rectangle-stack',
                'url' => route('products.election.home') . '#election-setup',
            ],
            [
                'id' => 4,
                'title' => 'Survey',
                'description' => 'Secure and scalable cloud storage solution for enterprises',
                'icon' => 'heroicon-o-document-text',
                'url' => route('products.election.home') . '#election-setup',
                'badge' => 'Free',
            ],

        ];
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }
}
