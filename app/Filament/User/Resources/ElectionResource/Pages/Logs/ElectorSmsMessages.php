<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\SmsMessagePurpose;
use App\Enums\SmsMessageStatus;
use App\Filament\Exports\ElectorSmsMessageExporter;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Election;
use App\Models\Elector;
use App\Models\SmsMessage;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class ElectorSmsMessages extends ElectionPage implements HasTable
{
    use HasTabs;
    use InteractsWithTable;

    protected static string $view = 'filament-panels::resources.pages.list-records';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Elector Reports';

    public ?string $activeTab = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election)
            && (
                self::hasBallotLink($election)
                || self::hasMfa($election)
                || self::hasVotedConfirmation($election)
            );
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && static::canAccessPage($parameters['record']);
    }

    public static function getNavigationLabel(): string
    {
        return 'SMS Logs';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getTableQuery())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->columns([
                TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('smsable.display_name')
                    ->description(description: fn (SmsMessage $smsMessage) => $smsMessage->smsable->membership_number)
                    ->label('Elector')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereHasMorph(
                                relation: 'smsable',
                                types: [Elector::class],
                                callback: fn (Builder $query) => $query
                                    ->where('membership_number', "$search")
                                    ->orWhere('full_name', 'like', "%$search%")
                            )
                    )
                    ->wrap(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->label(label: 'Sent at')
                    ->sortable()
                    ->wrap(),
            ])
            ->filters(filters: [
                SelectFilter::make('status')
                    ->options(options: SmsMessageStatus::class)
                    ->label(label: 'Status'),
            ])
            ->headerActions(actions: [
                ExportAction::make()
                    ->columnMapping(condition: false)
                    ->exporter(exporter: ElectorSmsMessageExporter::class)
                    ->fileName(name: fn (self $livewire): string => $livewire->getExportFileName())
                    ->icon(icon: 'heroicon-s-arrow-down-tray')
                    ->label(label: 'Export All')
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalSubmitActionLabel(label: 'Confirm')
                    ->modalHeading(heading: 'Export SMS Logs')
                    ->options(options: [
                        'timezone' => $this->getElection()->timezone,
                    ]),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return SmsMessage::query()
            ->whereHasMorph(
                relation: 'smsable',
                types: [Elector::class],
                callback: fn (Builder $query) => $query->whereMorphedTo(relation: 'event', model: $this->getElection())
            );
    }

    public function getTabs(): array
    {
        $tabs = [];

        if ($this->hasBallotLink($this->getElection())) {
            $tabs[SmsMessagePurpose::BallotLink->value] = Tab::make(label: SmsMessagePurpose::BallotLink->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::BallotLink->getTabQuery($query));
        }

        if ($this->hasMfa($this->getElection())) {
            $tabs[SmsMessagePurpose::BallotMfaCode->value] = Tab::make(label: SmsMessagePurpose::BallotMfaCode->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::BallotMfaCode->getTabQuery($query));
        }

        if ($this->hasVotedConfirmation($this->getElection())) {
            $tabs[SmsMessagePurpose::VotedConfirmation->value] = Tab::make(label: SmsMessagePurpose::VotedConfirmation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::VotedConfirmation->getTabQuery($query));
        }

        return $tabs;
    }

    protected function getExportFileName(): string
    {
        return Str::kebab($this->activeTab.'-sms-logs-').$this->getElection()->code;
    }

    public static function hasBallotLink(Election $election): bool
    {
        return $election->preference->ballot_link_sms;
    }

    public static function hasMfa(Election $election): bool
    {
        return $election->preference->mfa_sms;
    }

    public static function hasVotedConfirmation(Election $election): bool
    {
        return $election->preference->voted_confirmation_sms;
    }
}
