<?php

namespace App\Filament\Admin\Clusters;

use App\Models\User;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;

class SmsSettingsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user->hasAdminRole();
    }
}
