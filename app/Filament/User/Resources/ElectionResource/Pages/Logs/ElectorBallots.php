<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\ElectionCollaboratorPermission;
use App\Filament\Exports\ElectorBallotExporter;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Election;
use App\Models\Elector;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class ElectorBallots extends ElectionPage implements HasTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament-panels::resources.pages.list-records';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box';

    public ?string $activeTab = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.logs.elector_ballots.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.user.election-resource.pages.logs.elector_ballots.navigation_group');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->getTableQuery()
                    ->when(
                        value: ! $this->hasReadAccess(),
                        callback: fn (Builder $query) => $query->whereKey(null)
                    )
            )
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->columns([
                Tables\Columns\TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('display_name')
                    ->description(description: fn (Elector $record) => $record->membership_number)
                    ->label('Elector')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->where('membership_number', "$search")
                            ->orWhere('full_name', 'like', "%$search%")
                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(label: 'Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(label: 'Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ballot.booth.name')
                    ->label(label: 'Booth')
                    ->visible(condition: $this->getElection()->preference?->booth_voting),

                Tables\Columns\TextColumn::make('ballot.voted_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->label(label: 'Voted at')
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'ballot.ip_address')
                    ->label(label: 'IP Address'),
            ])
            ->headerActions(actions: [
                Tables\Actions\ExportAction::make()
                    ->columnMapping(condition: false)
                    ->exporter(exporter: ElectorBallotExporter::class)
                    ->fileName(name: fn (self $livewire): string => $livewire->getExportFileName())
                    ->icon(icon: 'heroicon-s-arrow-down-tray')
                    ->label(label: 'Export All')
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalSubmitActionLabel(label: 'Confirm')
                    ->modalHeading(heading: 'Export Elector Ballot Logs')
                    ->options(options: [
                        'timezone' => $this->getElection()->timezone,
                    ])
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),
            ]);
    }

    protected function getTableQuery(): Builder | Relation | null
    {
        return Elector::query()
            ->whereMorphedTo(relation: 'event', model: $this->getElection());
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(label: 'All'),

            'voted' => Tab::make(label: 'Voted')
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->withWhereHas(relation: 'ballot')),

            'non_voted' => Tab::make(label: 'Non-Voted')
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->doesntHave(relation: 'ballot')),
        ];
    }

    protected function getExportFileName(): string
    {
        return Str::kebab('elector-ballot-logs-') . $this->getElection()->code;
    }

    public static function canAccessPage(Election $election): bool
    {
        return ! $election->is_draft && ! $election->is_cancelled;
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->elector_logs !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->elector_logs === ElectionCollaboratorPermission::FullAccess;
    }
}
