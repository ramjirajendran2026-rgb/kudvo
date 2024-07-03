<?php

namespace App\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class SmsSettingsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
}
