<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\User\Resources\NomineeResource;
use App\Models\Nomination;
use App\Models\Nominee;
use Exception;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Nominees extends NominationPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.nomination-resource.pages.nominees';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function getRelationshipName(): string
    {
        return 'nominees';
    }

    public function getOwnerRecord(): Nomination
    {
        return $this->getNomination();
    }

    public static function getNavigationLabel(): string
    {
        return 'Nominees';
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return NomineeResource::table(table: $table)
            ->actions(actions: [
                ActionGroup::make(actions: [
                    $this->getApproveAction(),

                    $this->getRejectAction(),
                ]),
            ]);
    }

    protected function getApproveAction(): TableAction
    {
        return NomineeResource::getApproveAction()
            ->visible(condition: fn (self $livewire, Nominee $nominee): bool => $livewire->canApprove(nominee: $nominee));
    }

    protected function getRejectAction(): TableAction
    {
        return NomineeResource::getRejectAction()
            ->visible(condition: fn (self $livewire, Nominee $nominee): bool => $livewire->canReject(nominee: $nominee));
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccess($parameters);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return filled(value: $nomination = ($parameters['record'] ?? null)) &&
            static::canAccessPage(nomination: $nomination);
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return parent::canAccessPage(nomination: $nomination) &&
            static::can(action: 'viewAnyNominee', nomination: $nomination);
    }

    protected function canApprove(?Nominee $nominee = null): bool
    {
        if (filled(value: $nominee) && ! $nominee->isScrutinyPending()) {
            return false;
        }

        return static::can(action: 'approveAnyNominee', nomination: $this->getNomination());
    }

    protected function canReject(?Nominee $nominee = null): bool
    {
        if (filled(value: $nominee) && ! $nominee->isScrutinyPending()) {
            return false;
        }

        return static::can(action: 'rejectAnyNominee', nomination: $this->getNomination());
    }
}
