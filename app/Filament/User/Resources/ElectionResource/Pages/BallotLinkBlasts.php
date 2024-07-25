<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionCollaboratorPermission;
use App\Events\BallotLinkBlastCompleted;
use App\Events\BallotLinkBlastInitiated;
use App\Filament\User\Resources\BallotLinkBlastResource;
use App\Models\Election;
use Filament\Facades\Filament;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class BallotLinkBlasts extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.electors';

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $activeNavigationIcon = 'heroicon-s-bell-alert';

    public function getListeners(): array
    {
        return [
            'echo-private:elections.' . $this->getElection()->id . ',.' . BallotLinkBlastInitiated::getBroadcastName() => '$refresh',
            'echo-private:elections.' . $this->getElection()->id . ',.' . BallotLinkBlastCompleted::getBroadcastName() => '$refresh',
        ];
    }

    public static function getRelationshipName(): string
    {
        return 'ballotLinkBlasts';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election)
            && $election->preference?->isBallotLinkBlastNeeded();
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->ballot_link_blasts !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->ballot_link_blasts === ElectionCollaboratorPermission::FullAccess;
    }

    public static function getNavigationLabel(): string
    {
        return 'Ballot Link Blasts';
    }

    public function table(Table $table): Table
    {
        return BallotLinkBlastResource::table(table: $table);
    }
}
