<?php

namespace App\Filament\User\Resources;

use App\Actions\Meeting\PublishMeeting;
use App\Actions\Meeting\ScheduleMeetingLinkBlast;
use App\Filament\User\Resources\MeetingResource\Pages;
use App\Filament\User\Resources\MeetingResource\Widgets\MeetingOnboardingWidget;
use App\Filament\User\Resources\MeetingResource\Widgets\MeetingStatsOverview;
use App\Forms\Components\TimezonePicker;
use App\Models\Meeting;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $activeNavigationIcon = 'heroicon-s-calendar';

    protected static ?string $recordTitleAttribute = 'name';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->schema(components: [
                        static::getNameFormComponent(),

                        static::getDescriptionFormComponent(),
                    ]),

                Section::make('Voting Period')
                    ->columns()
                    ->schema([
                        static::getTimezoneFormComponent()
                            ->columnSpanFull(),

                        static::getVotingStartsAtFormComponent()
                            ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                        static::getVotingEndsAtFormComponent()
                            ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                Tables\Columns\TextColumn::make(name: 'code')
                    ->copyable()
                    ->icon(icon: 'heroicon-m-clipboard-document')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->size(size: TextColumnSize::Large)
                    ->weight(weight: FontWeight::SemiBold),

                Tables\Columns\TextColumn::make(name: 'name')
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->emptyStateDescription(description: __(key: 'filament-tables::table.empty.description', replace: [
                'model' => static::getPluralModelLabel(),
            ]))
            ->recordUrl(url: fn (Meeting $record) => Pages\MeetingDashboard::getUrl(parameters: [$record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMeetings::route(path: '/'),
            'create' => Pages\CreateMeeting::route(path: '/create'),
            'edit' => Pages\EditMeeting::route(path: '/{record}/edit'),
            'participants' => Pages\MeetingParticipants::route(path: '/{record}/participants'),
            'resolutions' => Pages\MeetingResolutions::route(path: '/{record}/resolutions'),
            'link_blasts' => Pages\MeetingLinkBlasts::route(path: '/{record}/link-blasts'),

            'reports.emails' => Pages\Reports\ParticipantEmails::route(path: '/{record}/reports/emails'),
            'reports.sms_messages' => Pages\Reports\ParticipantSmsMessages::route(path: '/{record}/reports/sms-messages'),

            'view' => Pages\MeetingDashboard::route(path: '/{record}'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page
            ->generateNavigationItems(components: [
                Pages\MeetingDashboard::class,
                Pages\MeetingParticipants::class,
                Pages\MeetingResolutions::class,
                Pages\MeetingLinkBlasts::class,
                Pages\Reports\ParticipantEmails::class,
                Pages\Reports\ParticipantSmsMessages::class,
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            MeetingOnboardingWidget::class,
            MeetingStatsOverview::class,
        ];
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->icon(icon: 'heroicon-m-pencil-square');
    }

    public static function getDeleteAction(): DeleteAction
    {
        return DeleteAction::make();
    }

    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record); // TODO: Change the autogenerated stub
    }

    public static function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->action(action: function (Meeting $meeting, Action $action, array $data, PublishMeeting $publishAction, ScheduleMeetingLinkBlast $blast): void {
                $publishAction->execute(meeting: $meeting);

                $blast->execute(meeting: $meeting, scheduledAt: $data['scheduled_at'] ?? now());

                $action->success();
            })
            ->color(color: 'warning')
            ->form(form: [
                ToggleButtons::make(name: 'notify_participants')
                    ->colors(['warning', 'warning'])
                    ->default(state: true)
                    ->helperText(text: 'Delivery may take several minutes, depending on the number of participants. Please plan accordingly.')
                    ->inline()
                    ->label(label: 'Send meeting links to participants')
                    ->live()
                    ->options(options: [
                        true => 'Immediately',
                        false => 'Later',
                    ]),

                DateTimePicker::make(name: 'scheduled_at')
                    ->hiddenLabel()
                    ->label(label: 'Schedule at')
                    ->minDate(date: fn (Meeting $meeting): string => now($meeting->timezone)->format('Y-m-d H:i'))
                    ->required()
                    ->seconds(condition: false)
                    ->timezone(timezone: fn (Meeting $meeting): ?string => $meeting->timezone)
                    ->visible(condition: fn (Get $get): bool => $get('notify_participants') == false),
            ])
            ->icon(icon: 'heroicon-m-rocket-launch')
            ->label(label: 'Publish')
            ->modalIcon(icon: 'heroicon-m-rocket-launch')
            ->successNotificationTitle(title: 'Meeting published');
    }

    public static function getNameFormComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->charCounter(count: 255)
            ->maxLength(length: 255)
            ->required();
    }

    public static function getDescriptionFormComponent(): TiptapEditor
    {
        return TiptapEditor::make(name: 'description')
            ->maxContentWidth('full');
    }

    public static function getTimezoneFormComponent(): TimezonePicker
    {
        return TimezonePicker::make()
            ->default(Filament::getTenant()?->timezone ?? request()->ipinfo?->timezone)
            ->required();
    }

    public static function getVotingStartsAtFormComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'voting_starts_at')
            ->label(label: __('filament.user.election-resource.form.starts_at.label'))
            ->required()
            ->seconds(condition: false);
    }

    public static function getVotingEndsAtFormComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'voting_ends_at')
            ->after(date: 'voting_starts_at')
            ->label(label: __('filament.user.election-resource.form.ends_at.label'))
            ->required()
            ->seconds(condition: false);
    }
}
