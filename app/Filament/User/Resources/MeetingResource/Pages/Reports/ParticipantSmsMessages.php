<?php

namespace App\Filament\User\Resources\MeetingResource\Pages\Reports;

use App\Enums\SmsMessagePurpose;
use App\Enums\SmsMessageStatus;
use App\Filament\User\Resources\MeetingResource;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\SmsMessage;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ParticipantSmsMessages extends ManageRelatedRecords
{
    protected static string $resource = MeetingResource::class;

    protected static string $relationship = 'participantSmsMessages';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    public function getBreadcrumb(): string
    {
        return 'Reports';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationLabel(): string
    {
        return 'SMS';
    }

    public function getTitle(): string | Htmlable
    {
        return static::getNavigationLabel();
    }

    public function getTabs(): array
    {
        return collect(SmsMessagePurpose::cases())
            ->filter(callback: fn (SmsMessagePurpose $purpose) => $purpose->getEventType() === Meeting::class)
            ->mapWithKeys(callback: fn (SmsMessagePurpose $purpose) => [
                $purpose->value => Tab::make(label: $purpose->getLabel())
                    ->modifyQueryUsing(callback: fn (Builder $query) => $purpose->getTabQuery($query)),
            ])
            ->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('smsable.name')
                    ->description(description: fn (SmsMessage $smsMessage) => $smsMessage->smsable->membership_number)
                    ->label('Participant')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereHasMorph(
                                relation: 'smsable',
                                types: [Participant::class],
                                callback: fn (Builder $query) => $query
                                    ->where('membership_number', "$search")
                                    ->orWhere('name', 'like', "%$search%")
                            )
                    )
                    ->wrap(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime(timezone: $this->getOwnerRecord()->timezone)
                    ->label(label: 'Sent at')
                    ->sortable()
                    ->wrap(),
            ])
            ->defaultSort(column: 'created_at', direction: 'desc')
            ->filters(filters: [
                SelectFilter::make('status')
                    ->options(options: SmsMessageStatus::class)
                    ->label(label: 'Status'),
            ]);
    }
}
