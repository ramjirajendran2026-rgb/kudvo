<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Enums\MeetingLinkBlastStatus;
use App\Events\MeetingLinkBlastCompleted;
use App\Events\MeetingLinkBlastInitiated;
use App\Filament\User\Resources\MeetingResource;
use App\Models\Meeting;
use App\Models\MeetingLinkBlast;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class MeetingLinkBlasts extends ManageRelatedRecords
{
    protected static string $resource = MeetingResource::class;

    protected static string $relationship = 'meetingLinkBlasts';

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $activeNavigationIcon = 'heroicon-s-bell-alert';

    public static function getNavigationLabel(): string
    {
        return 'Link Blasts';
    }

    public function getBreadcrumbs(): array
    {
        return [
            MeetingResource::getUrl() => MeetingResource::getBreadcrumb(),
            MeetingDashboard::getUrl(parameters: ['record' => $this->getRecord()]) => $this->getRecordTitle(),
        ];
    }

    public function getListeners(): array
    {
        return [
            'echo-private:meetings.' . $this->getRecord()->getKey() . ',.' . MeetingLinkBlastInitiated::getBroadcastName() => '$refresh',
            'echo-private:meetings.' . $this->getRecord()->getKey() . ',.' . MeetingLinkBlastCompleted::getBroadcastName() => '$refresh',
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return static::getNavigationLabel();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema([
                DateTimePicker::make(name: 'scheduled_at')
                    ->helperText(text: 'Delivery may take several minutes, depending on the number of participants. Please plan accordingly.')
                    ->hiddenLabel()
                    ->label(label: 'Schedule at')
                    ->minDate(date: fn (Meeting $meeting): string => now($meeting->timezone)->format('Y-m-d H:i'))
                    ->required()
                    ->seconds(condition: false)
                    ->timezone(timezone: fn (Meeting $meeting): ?string => $meeting->timezone),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                static::getTableEditAction(),
                static::getTableDeleteAction(),
            ])
            ->columns(components: [
                TextColumn::make(name: '#')
                    ->rowIndex(),

                TextColumn::make(name: 'scheduled_at')
                    ->dateTime(format: 'M j, Y h:i A')
                    ->timezone(timezone: fn (Meeting $meeting): ?string => $meeting->timezone),

                TextColumn::make(name: 'status')
                    ->badge(),
            ])
            ->headerActions(actions: [
                static::getTableCreateAction(),
            ])
            ->modelLabel(label: 'Link Blast')
            ->recordTitleAttribute(attribute: 'scheduled_at');
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->icon(icon: 'heroicon-m-plus')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalHeading(heading: 'Schedule a blast')
            ->modelLabel(label: 'Blast')
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->visible(condition: fn (MeetingLinkBlast $record): bool => $record->status === MeetingLinkBlastStatus::Scheduled);
    }

    public static function getTableDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton()
            ->visible(condition: fn (MeetingLinkBlast $record): bool => $record->status === MeetingLinkBlastStatus::Scheduled);
    }
}
