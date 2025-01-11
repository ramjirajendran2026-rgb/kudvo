<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Enums\MeetingOnboardingStep;
use App\Filament\User\Resources\MeetingResource;
use App\Filament\User\Resources\ParticipantResource;
use App\Models\Meeting;
use App\Models\Participant;
use Database\Factories\ParticipantFactory;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rules\Unique;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class MeetingParticipants extends ManageRelatedRecords
{
    use Concerns\UsesMeetingOnboardingWidget;

    protected static string $resource = MeetingResource::class;

    protected static string $relationship = 'participants';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->currentOnboardingStep = MeetingOnboardingStep::AddParticipants;
        $this->pendingOnboardingStep = $this->getRecord()->getOnboardingStep();
    }

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'];
        if (! $record instanceof Meeting) {
            $record = MeetingResource::resolveRecordRouteBinding($record);
        }

        $pendingStep = $record->getOnboardingStep();
        if ($pendingStep && $pendingStep->getIndex() < MeetingOnboardingStep::AddParticipants->getIndex()) {
            return false;
        }

        return true;
    }

    public static function getNavigationLabel(): string
    {
        return 'Participants';
    }

    public function getBreadcrumbs(): array
    {
        return [
            MeetingResource::getUrl() => MeetingResource::getBreadcrumb(),
            MeetingDashboard::getUrl(parameters: ['record' => $this->getRecord()]) => $this->getRecordTitle(),
        ];
    }

    public function getCurrentOnboardingStep(): MeetingOnboardingStep
    {
        return MeetingOnboardingStep::AddParticipants;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Participants';
    }

    public function form(Form $form): Form
    {
        return ParticipantResource::form(form: $form)
            ->columns(columns: null)
            ->schema(components: [
                ParticipantResource::getNameFormComponent(),

                ParticipantResource::getMembershipNumberFormComponent()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule
                            ->where(column: 'meeting_id', value: $this->getRecord()->getKey())
                    ),

                ParticipantResource::getEmailFormComponent(),

                ParticipantResource::getPhoneFormComponent(),

                ParticipantResource::getWeightageFormComponent(),
            ]);
    }

    public function table(Table $table): Table
    {
        return ParticipantResource::table(table: $table)
            ->actions(actions: [
                $this->getSendMeetingLinkAction(),

                ParticipantResource::getEditTableAction(),

                ParticipantResource::getDeleteTableAction()
                    ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self()),
            ])
            ->bulkActions(actions: [
                BulkActionGroup::make(actions: [
                    ParticipantResource::getDeleteBulkAction()
                        ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self()),
                ]),
            ])
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->description(description: fn (Participant $record): ?string => $record->membership_number)
                    ->searchable(condition: ['name', 'membership_number'])
                    ->wrap(),

                TextColumn::make(name: 'email')
                    ->searchable(),

                PhoneColumn::make(name: 'phone')
                    ->displayFormat(format: PhoneInputNumberType::E164),

                TextColumn::make(name: 'weightage')
                    ->alignCenter()
                    ->numeric(),
            ])
            ->headerActions(actions: [
                ParticipantResource::getImportTableAction()
                    ->fillForm([
                        'phone_country' => Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'),
                    ])
                    ->options(options: ['meeting_id' => $this->getRecord()->getKey()]),

                ParticipantResource::getCreateTableAction()
                    ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self())
                    ->createAnother(condition: false),

                ActionGroup::make(actions: [
                    $this->getGenerateDummyParticipantsAction()
                        ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self()),
                ]),
            ])
            ->recordTitleAttribute(attribute: 'name');
    }

    protected function getSendMeetingLinkAction(): TableAction
    {
        return TableAction::make(name: 'sendMeetingLink')
            ->requiresConfirmation()
            ->action(action: function (self $livewire, Participant $participant, TableAction $action) {
                $participant->sendParticipationLink(meeting: $livewire->getOwnerRecord());

                $action->success();
            })
            ->icon(icon: 'heroicon-m-bell-alert')
            ->iconButton()
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: 'Meeting link sent')
            );
    }

    public function getGenerateDummyParticipantsAction(): TableAction
    {
        return TableAction::make('generateDummyParticipants')
            ->requiresConfirmation()
            ->action(function (self $livewire, TableAction $action, array $data) {
                Participant::factory($data['count'])
                    ->for($livewire->getRecord())
                    ->when($data['with_name'], fn (ParticipantFactory $factory) => $factory->withName())
                    ->when($data['with_email'], fn (ParticipantFactory $factory) => $factory->withEmail())
                    ->when($data['with_phone'], fn (ParticipantFactory $factory) => $factory->withPhone())
                    ->when($data['with_weightage'], fn (ParticipantFactory $factory) => $factory->withWeightage())
                    ->create();

                $action->success();
            })
            ->form([
                TextInput::make('count')
                    ->default(10)
                    ->integer()
                    ->maxValue(99999)
                    ->minValue(1)
                    ->required(),

                Toggle::make('with_name')
                    ->default(true),

                Toggle::make('with_email')
                    ->default(true),

                Toggle::make('with_phone')
                    ->default(true),

                Toggle::make('with_weightage')
                    ->default(true),
            ])
            ->successNotificationTitle('Generated successfully');
    }
}
