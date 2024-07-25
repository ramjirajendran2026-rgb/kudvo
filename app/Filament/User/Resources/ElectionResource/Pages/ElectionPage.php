<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Data\Election\VoteSecretData;
use App\Enums\ElectionSetupStep;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasElectorGroups;
use App\Filament\User\Resources\ElectionResource;
use App\Forms\Components\VotePicker;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord\Concerns\Translatable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Markdown;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Form $form
 */
abstract class ElectionPage extends Page implements HasElection, HasElectorGroups
{
    use Translatable;

    protected static string $resource = ElectionResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    #[Locked]
    public Election $election;

    #[Locked]
    public array $electorGroups = [];

    public function mount(int | string $record): void
    {
        $this->election = $this->resolveElection(key: $record);

        $this->authorizeAccess();

        $this->electorGroups = $this->getElection()->preference?->candidate_group
            ? $this->getElection()->getElectorGroups() : [];
    }

    public function mountCanAuthorizeAccess(): void {}

    protected function resolveElection(int | string $key): Election
    {
        /** @var Election $nomination */
        $nomination = app(ElectionResource::getModel())
            ->resolveRouteBindingQuery(ElectionResource::getEloquentQuery(), $key, ElectionResource::getRecordRouteKeyName())
            ->withCount(relations: ['positions', 'electors'])
            ->first();

        abort_if(boolean: blank(value: $nomination), code: Response::HTTP_NOT_FOUND);

        return $nomination;
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getPendingStep(): ?ElectionSetupStep
    {
        return $this->getElection()->getPendingStep();
    }

    public function getCurrentStep(): ?ElectionSetupStep
    {
        return null;
    }

    public function getElectorGroups(): array
    {
        return $this->electorGroups;
    }

    public function getTitle(): string | Htmlable
    {
        return static::getNavigationLabel() . ' - ' . $this->getRecordTitle();
    }

    public function getRecordTitle(): string | Htmlable
    {
        if (! ElectionResource::hasRecordTitle()) {
            return ElectionResource::getTitleCaseModelLabel();
        }

        return ElectionResource::getRecordTitle($this->getElection());
    }

    public function getHeading(): string | Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (! $this->getElection()->isTimingConfigured()) {
            return null;
        }

        return Markdown::inline(text: __('filament.user.election-resource.pages.base.subheading', ['starts' => $this->getElection()->starts_at_local->format(format: 'M d, Y h:i A (T)'), 'ends' => $this->getElection()->ends_at_local->format(format: 'M d, Y h:i A (T)')]));
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getElection(),
        ];
    }

    public function getSubNavigation(): array
    {
        return filled($this->getCurrentStep()) && filled($this->getPendingStep()) ? [] : ElectionResource::getRecordSubNavigation($this);
    }

    public function getWidgetData(): array
    {
        return [
            'record' => $this->getElection(),
            'election' => $this->getElection(),
        ];
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return $this->getElection();
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        if (! static::canAccessPage(election: $this->election)) {
            $this->notifyUnauthorized();

            $this->redirect(Dashboard::getUrl(parameters: [$this->election]));
        }
    }

    public function notifyUnauthorized(): void
    {
        Notification::make()
            ->title(title: __('filament.user.election-resource.pages.base.access_denied.notification.title'))
            ->body(body: __('filament.user.election-resource.pages.base.access_denied.notification.body'))
            ->warning()
            ->send();
    }

    public function notifyImportCompletion(array $event): void
    {
        $import = Import::with(relations: 'user')->find(id: $event['importId']);

        if (! $import->user instanceof Authenticatable) {
            return;
        }

        $failedRowsCount = $import->getFailedRowsCount();

        Notification::make()
            ->persistent()
            ->title($import->importer::getCompletedNotificationTitle($import))
            ->body($import->importer::getCompletedNotificationBody($import))
            ->when(
                ! $failedRowsCount,
                fn (Notification $notification) => $notification->success(),
            )
            ->when(
                $failedRowsCount && ($failedRowsCount < $import->total_rows),
                fn (Notification $notification) => $notification->warning(),
            )
            ->when(
                $failedRowsCount === $import->total_rows,
                fn (Notification $notification) => $notification->danger(),
            )
            ->when(
                $failedRowsCount,
                fn (Notification $notification) => $notification->actions([
                    NotificationAction::make('downloadFailedRowsCsv')
                        ->label('Download failed rows')
                        ->color('danger')
                        ->icon(icon: 'heroicon-m-arrow-down-tray')
                        ->url(route('filament.imports.failed-rows.download', ['import' => $import], absolute: false), shouldOpenInNewTab: true)
                        ->markAsRead(),
                ]),
            )
            ->send();
    }

    public function getRedirectUrl(): string
    {
        $params = $this->getSubNavigationParameters();

        return $this->getElection()->getPendingStep()?->getUrl($params) ?? Dashboard::getUrl($params);
    }

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getElection())
            ->recordTitle($this->getRecordTitle());
    }

    protected function getHeaderActions(): array
    {
        return [
            ElectionResource::getEditAction()
                ->iconButton(),
        ];
    }

    protected function getPreviewBallotAction()
    {
        return Action::make(name: 'previewBallot')
            ->authorize(abilities: 'preview')
            ->action(action: function (Action $action, array $data, Form $form): void {
                $preview = $data['preview'];

                if ($preview) {
                    Notification::make()
                        ->title(title: 'Preview completed')
                        ->success()
                        ->send();

                    return;
                }

                $data['preview'] = true;
                $data['votes'] = Arr::mapWithKeys($data['votes'], fn ($item, $key) => [$key => Arr::map($item, fn (VoteSecretData $subItem) => $subItem->key)]);

                $form->fill(state: $data);

                $action->formData(data: $data);
                $action->halt();
            })
            ->color(color: 'success')
            ->form(
                form: fn (HasElection $livewire): array => [
                    Hidden::make(name: 'preview')
                        ->default(state: false),

                    Group::make()
                        ->statePath(path: 'votes')
                        ->schema(
                            components: $livewire->getElection()->positions
                                ->map(
                                    callback: fn (Position $position) => VotePicker::makeFor(position: $position)
                                        ->candidateGroup(condition: $this->getElection()->preference->candidate_group)
                                        ->disabled(condition: fn (Get $get): bool => $get(path: '../preview'))
                                        ->photo(condition: $this->getElection()->preference->candidate_photo)
                                        ->preview(condition: fn (Get $get): bool => $get(path: '../preview'))
                                        ->symbol(condition: $this->getElection()->preference->candidate_symbol),
                                )
                                ->toArray(),
                        ),
                ]
            )
            ->icon(icon: 'heroicon-m-eye')
            ->label(label: __('filament.user.election-resource.actions.preview.label'))
            ->modalCancelAction(action: false)
            ->modalDescription(description: $this->getSubheading())
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalHeading(heading: $this->getHeading())
            ->modalSubmitActionLabel(label: fn (array $data): string => ($data['preview'] ?? false) ? 'Confirm' : 'Continue')
            ->slideOver();
    }

    public function getCollaboratorsPageAction()
    {
        return Action::make(name: 'collaborators')
            ->icon(icon: 'heroicon-m-users')
            ->label(label: __('filament.user.election-resource.actions.collaborators.label'))
            ->url(url: Collaborators::getUrl(parameters: [$this->getElection()]))
            ->hidden(condition: blank($this->getPendingStep()))
            ->visible(condition: Collaborators::canAccessPage(election: $this->getElection()));
    }

    public static function can(string $action, Election $election): bool
    {
        return ElectionResource::can(action: $action, record: $election);
    }

    public static function cannot(string $action, Election $election): bool
    {
        return ! static::can(action: $action, election: $election);
    }

    public static function canAccessPage(Election $election): bool
    {
        return ElectionResource::canView(record: $election);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && static::canAccessPage($parameters['record']);
    }

    public function isOwner(): bool
    {
        return $this->getElection()->isOwner(Filament::auth()->user());
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner();
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner();
    }
}
