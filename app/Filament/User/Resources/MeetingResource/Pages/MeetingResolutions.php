<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Enums\MeetingOnboardingStep;
use App\Enums\ResolutionChoice;
use App\Filament\User\Resources\MeetingResource;
use App\Filament\User\Resources\ResolutionResource;
use App\Models\Meeting;
use App\Models\Resolution;
use Blade;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Concerns\CanAuthorizeAccess;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

class MeetingResolutions extends ViewRecord
{
    use CanAuthorizeAccess;
    use Concerns\UsesMeetingOnboardingWidget;

    protected static string $resource = MeetingResource::class;

    protected static string $relationship = 'resolutions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->currentOnboardingStep = MeetingOnboardingStep::AddResolutions;
        $this->pendingOnboardingStep = $this->getRecord()->getOnboardingStep();
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (blank($parameters['record'] ?? null)) {
            return parent::canAccess($parameters);
        }

        $record = $parameters['record'];
        if (! $record instanceof Meeting) {
            $record = MeetingResource::resolveRecordRouteBinding($record);
        }

        $pendingStep = $record->getOnboardingStep();
        if ($pendingStep && $pendingStep->getIndex() < MeetingOnboardingStep::AddResolutions->getIndex()) {
            return false;
        }

        return true;
    }

    public static function getNavigationLabel(): string
    {
        return 'Resolutions';
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
        return MeetingOnboardingStep::AddResolutions;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Resolutions';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(components: [
                RepeatableEntry::make(name: 'resolutions')
                    ->columnSpanFull()
                    ->extraAttributes(attributes: [
                        'class' => 'resolution-repeatable-entry',
                    ])
                    ->hiddenLabel()
                    ->schema(components: [
                        Section::make(heading: static fn (Resolution $record): string => $record->name)
                            ->collapsible()
                            ->columns(columns: 3)
                            ->headerActions(actions: [
                                $this->getEditResolutionAction(),

                                $this->getDeleteResolutionAction(),
                            ])
                            ->schema(components: [
                                TextEntry::make(name: 'description')
                                    ->color('-')
                                    ->columnSpanFull()
                                    ->extraAttributes(attributes: [
                                        'class' => 'resolution-description',
                                    ])
                                    ->hiddenLabel()
                                    ->html()
                                    ->size(size: '-'),

                                TextEntry::make(name: 'for_label')
                                    ->alignCenter()
                                    ->color(color: ResolutionChoice::For->getColor())
                                    ->hiddenLabel()
                                    ->icon(icon: ResolutionChoice::For->getIcon())
                                    ->iconColor(color: ResolutionChoice::For->getColor())
                                    ->size(size: TextEntry\TextEntrySize::Large)
                                    ->weight(weight: FontWeight::SemiBold),

                                TextEntry::make(name: 'against_label')
                                    ->alignCenter()
                                    ->color(color: ResolutionChoice::Against->getColor())
                                    ->hiddenLabel()
                                    ->icon(icon: ResolutionChoice::Against->getIcon())
                                    ->iconColor(color: ResolutionChoice::Against->getColor())
                                    ->size(size: TextEntry\TextEntrySize::Large)
                                    ->weight(weight: FontWeight::SemiBold),

                                TextEntry::make(name: 'abstain_label')
                                    ->alignCenter()
                                    ->color(color: ResolutionChoice::Abstain->getColor())
                                    ->hiddenLabel()
                                    ->icon(icon: ResolutionChoice::Abstain->getIcon())
                                    ->iconColor(color: ResolutionChoice::Abstain->getColor())
                                    ->size(size: TextEntry\TextEntrySize::Large)
                                    ->weight(weight: FontWeight::SemiBold)
                                    ->visible(condition: static fn (Resolution $record): bool => $record->allow_abstain_votes),
                            ]),
                    ]),

                Section::make()
                    ->hidden(condition: fn (Meeting $record): bool => $record->resolutions()->exists())
                    ->schema(components: [
                        TextEntry::make(name: 'emptyState')
                            ->alignCenter()
                            ->getStateUsing(callback: fn (): Htmlable => new HtmlString(Blade::render(
                                <<<'BLADE'
<x-filament-tables::empty-state
    icon="heroicon-o-x-mark"
    heading="No resolutions"
    description="Create a resolution to get started."
/>
BLADE
                            )))
                            ->hiddenLabel(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getReorderResolutionAction(),

            $this->getCreateResolutionAction(),
        ];
    }

    protected function getFooterActions(): array
    {
        return [
            $this->getPreviousPageAction(),

            $this->getNextPageAction(),
        ];
    }

    protected function getCreateResolutionAction(): CreateAction
    {
        return CreateAction::make()
            ->authorize(fn (self $livewire): bool => Gate::check('createResolution', [$livewire->getRecord()]))
            ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self())
            ->form(form: static fn (Form $form): Form => ResolutionResource::form(form: $form))
            ->model(model: Resolution::class)
            ->modalCancelAction(action: false)
            ->modalWidth(width: MaxWidth::FourExtraLarge)
            ->record(record: null)
            ->relationship(relationship: static fn (self $livewire): Relation => $livewire->getRecord()->resolutions())
            ->stickyModalFooter()
            ->stickyModalHeader();
    }

    protected function getReorderResolutionAction(): EditAction
    {
        return EditAction::make(name: 'reorderResolution')
            ->authorize(fn (self $livewire, Resolution $state): bool => Gate::check('reorderResolution', [$livewire->getRecord(), $state]))
            ->form(form: [
                Repeater::make(name: 'resolutions')
                    ->addable(condition: false)
                    ->deletable(condition: false)
                    ->hiddenLabel()
                    ->orderColumn()
                    ->relationship()
                    ->reorderable()
                    ->simple(field: TextInput::make(name: 'name')->disabled()),
            ])
            ->icon(icon: 'heroicon-m-arrows-up-down')
            ->label(label: 'Reorder')
            ->labeledFrom(breakpoint: 'lg')
            ->modalHeading(heading: 'Reorder Resolutions')
            ->modalWidth(width: MaxWidth::FourExtraLarge)
            ->outlined()
            ->stickyModalFooter()
            ->stickyModalHeader()
            ->url(url: null);
    }

    protected function getEditResolutionAction(): Action
    {
        return Action::make(name: 'editResolution')
            ->authorize(fn (self $livewire, Resolution $state): bool => Gate::check('updateResolution', [$livewire->getRecord(), $state]))
            ->action(action: static function (Action $action, Model $record, array $data): void {
                $record->update(attributes: $data);

                $action->success();
            })
            ->fillForm(data: static fn (Model $record): array => $record->attributesToArray())
            ->form(form: static fn (Form $form, Model $record): Form => ResolutionResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->label(label: __('filament-actions::edit.single.label'))
            ->labeledFrom(breakpoint: 'lg')
            ->modalHeading(heading: static fn (Resolution $record): string => __('filament-actions::edit.single.modal.heading', ['label' => $record->name]))
            ->modalSubmitActionLabel(label: __('filament-actions::edit.single.modal.actions.save.label'))
            ->modalWidth(width: MaxWidth::FourExtraLarge)
            ->outlined()
            ->size(size: ActionSize::Small)
            ->stickyModalFooter()
            ->stickyModalHeader()
            ->successNotificationTitle(title: __('filament-actions::edit.single.notifications.saved.title'));
    }

    protected function getDeleteResolutionAction(): Action
    {
        return Action::make(name: 'deleteResolution')
            ->authorize(fn (self $livewire, Resolution $state): bool => Gate::check('deleteResolution', [$livewire->getRecord(), $state]))
            ->after(callback: fn () => $this->dispatch('meeting.onboarding.refresh')->self())
            ->requiresConfirmation()
            ->action(action: static function (Action $action, Model $record): void {
                $result = $record->delete();

                if (! $result) {
                    $action->failure();

                    return;
                }

                $action->success();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->label(__('filament-actions::delete.single.label'))
            ->labeledFrom(breakpoint: 'lg')
            ->modalHeading(heading: static fn (Resolution $record): string => __('filament-actions::delete.single.modal.heading', ['label' => $record->name]))
            ->modalIcon(icon: FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash')
            ->modalSubmitActionLabel(label: __('filament-actions::delete.single.modal.actions.delete.label'))
            ->outlined()
            ->size(size: ActionSize::Small)
            ->successNotificationTitle(title: __('filament-actions::delete.single.notifications.deleted.title'));
    }
}
