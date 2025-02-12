<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class ServicesCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $activeNavigationIcon = 'heroicon-s-key';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
}
