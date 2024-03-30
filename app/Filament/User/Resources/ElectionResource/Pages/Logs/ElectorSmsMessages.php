<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Elector;
use App\Models\Email;
use App\Models\SmsMessage;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ElectorSmsMessages extends ElectionPage implements HasTable
{
    use HasTabs;
    use InteractsWithTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.logs.elector-sms-messages';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Logs';

    public ?string $activeTab = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function getNavigationLabel(): string
    {
        return 'Elector SMS Messages';
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
                            ->where('smsable_type', Elector::class)
                            ->whereExists(function (QueryBuilder $query) use ($search) {
                                $query
                                    ->select('id')
                                    ->from('electors')
                                    ->whereColumn('electors.id', 'sms_messages.smsable_id')
                                    ->where(
                                        fn (QueryBuilder $query) => $query
                                            ->where('membership_number', "$search")
                                            ->orWhere('full_name', 'like', "%$search%")
                                    );
                            })
                    )
                    ->wrap(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->sortable()
                    ->wrap(),
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
        return [
            SmsMessagePurpose::BallotLink->value => Tab::make(label: SmsMessagePurpose::BallotLink->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::BallotLink->getTabQuery($query)),

            SmsMessagePurpose::BallotMfaCode->value => Tab::make(label: SmsMessagePurpose::BallotMfaCode->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::BallotMfaCode->getTabQuery($query)),

            SmsMessagePurpose::VotedConfirmation->value => Tab::make(label: SmsMessagePurpose::VotedConfirmation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => SmsMessagePurpose::VotedConfirmation->getTabQuery($query)),
        ];
    }
}
