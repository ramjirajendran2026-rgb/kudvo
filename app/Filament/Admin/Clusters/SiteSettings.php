<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class SiteSettings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
}
