<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionCollaboratorPermission;
use App\Enums\ElectionSetupStep;
use App\Enums\ElectionStatus;
use App\Events\Election\CandidateImportCompleted;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Imports\CandidateImporter;
use App\Filament\User\Resources\CandidateResource;
use App\Filament\User\Resources\ElectionResource\Widgets\ElectorDataImportProgress;
use App\Filament\User\Resources\PositionResource;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BallotSetup extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.ballot.setup';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $activeNavigationIcon = 'heroicon-s-list-bullet';

    protected function getListeners(): array
    {
        return [
            ...parent::getListeners(),

            'echo-private:elections.' . $this->getElection()->id . ',.' . CandidateImportCompleted::getBroadcastName() => 'notifyImportCompletion',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.ballot_setup.navigation_label');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(components: [
                RepeatableEntry::make(name: 'positions')
                    ->contained(condition: false)
                    ->extraAttributes(attributes: ['class' => 'position-repeatable-entry'])
                    ->hiddenLabel()
                    ->placeholder(placeholder: fn () => $this->generateEmptyStatePlaceholder(
                        heading: __('filament.user.election-resource.pages.ballot_setup.infolist.positions.empty_state.heading'),
                        description: __('filament.user.election-resource.pages.ballot_setup.infolist.positions.empty_state.description'),
                        icon: 'heroicon-o-archive-box',
                        actions: [$this->getCreatePositionAction]
                    ))
                    ->schema(components: [
                        Section::make(heading: fn (Position $state): ?string => $state->name)
                            ->compact()
                            ->description(
                                description: fn (Position $state): ?string => collect(value: [
                                    Str::plural(value: $state->quota . ' Post', count: $state->quota),
                                    ...($state->abstain ? [Str::plural(value: "Minimum $state->threshold selection", count: $state->threshold)] : []),
                                    ...($this->getElection()->preference?->segmented_ballot ? $state->segments()->pluck(column: 'name') : []),
                                    ...($state->isUnopposed() ? ['Unopposed'] : []),
                                ])->implode(value: ' • ')
                            )
                            ->footerActions(actions: [
                                $this->getCreateCandidateAction()
                                    ->size(size: ActionSize::Large)
                                    ->extraAttributes(attributes: ['class' => '!ring-0']),
                            ])
                            ->footerActionsAlignment(alignment: Alignment::Center)
                            ->headerActions(actions: [
                                $this->getReorderCandidateAction(),

                                $this->getEditPositionAction(),

                                $this->getDeletePositionAction(),
                            ])
                            ->schema(components: [
                                RepeatableEntry::make(name: 'allCandidates')
                                    ->extraAttributes(attributes: ['class' => 'candidate-repeatable-entry'])
                                    ->hiddenLabel()
                                    ->placeholder(placeholder: $this->generateEmptyStatePlaceholder(
                                        heading: __('filament.user.election-resource.pages.ballot_setup.infolist.positions.candidates.empty_state.heading'),
                                        description: __('filament.user.election-resource.pages.ballot_setup.infolist.positions.candidates.empty_state.description'),
                                        icon: 'heroicon-o-x-mark',
                                    ))
                                    ->schema(components: [
                                        Split::make(schema: [
                                            SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                ->circular()
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->photo_url)
                                                ->extraImgAttributes(['class' => 'aspect-square max-w-12 md:!max-w-20'])
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 'auto')
                                                ->visible(condition: $this->getElection()->preference?->candidate_photo),

                                            TextEntry::make(name: 'display_name')
                                                ->extraAttributes(attributes: fn (Candidate $record): array => $record->disabled ? ['class' => 'line-through'] : [])
                                                ->helperText(
                                                    text: fn (Candidate $record): ?string => collect(value: [
                                                        $record->membership_number,
                                                        $this->getElection()->preference->candidate_group ? $record->candidateGroup?->name : null,
                                                        ! $record->disabled && $record->position?->isUnopposed() ? 'Unopposed' : null,
                                                    ])
                                                        ->filter(callback: fn (?string $item): bool => filled($item))
                                                        ->implode(value: ' • ')
                                                )
                                                ->hiddenLabel()
                                                ->size(size: TextEntry\TextEntrySize::Large)
                                                ->suffixActions(actions: [
                                                    $this->getEditCandidateAction(),

                                                    $this->getDisableCandidateAction(),

                                                    $this->getEnableCandidateAction(),

                                                    $this->getDeleteCandidateAction(),
                                                ])
                                                ->weight(weight: FontWeight::Medium),

                                            SpatieMediaLibraryImageEntry::make(name: 'symbol')
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->symbol_url)
                                                ->extraImgAttributes(attributes: ['class' => 'rounded-xl bg-black aspect-square max-w-12 md:!max-w-20'])
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 'auto')
                                                ->visible(condition: $this->getElection()->preference?->candidate_symbol),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->record(
                record: $this->getElection()
                    ->load(relations: ['positions.allCandidates'])
            );
    }

    public function getCurrentStep(): ?ElectionSetupStep
    {
        return ElectionSetupStep::Ballot;
    }

    public function hasReadAccess(): bool
    {
        return $this->getElection()->isOwner(Filament::auth()->user())
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->ballot_setup !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->ballot_setup === ElectionCollaboratorPermission::FullAccess;
    }

    protected function generateEmptyStatePlaceholder(string $heading, ?string $description = null, ?string $icon = null, array $actions = []): HtmlString
    {
        return new HtmlString(
            html: Blade::render(
                string: <<<'HTML'
<x-filament.state
    :heading="$heading"
    :description="$description"
    :icon="$icon"
    :actions="$actions"
/>
HTML,
                data: [
                    'heading' => $heading,
                    'description' => $description,
                    'icon' => $icon,
                    'actions' => $actions,
                ]
            ),
        );
    }

    protected function getHeaderWidgets(): array
    {
        return $this->getImportProgressWidgets();
    }

    protected function getImportProgressWidgets(): array
    {
        if ($this->getElection()->status !== ElectionStatus::DRAFT) {
            return [];
        }

        return $this->getElection()
            ->candidateImports()
            ->whereNull(columns: 'completed_at')
            ->get()
            ->map(callback: fn (Import $import) => ElectorDataImportProgress::make(['import' => $import]))
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getReorderPositionAction(),

            $this->getCreatePositionAction(),

            $this->getPreviewBallotAction(),

            $this->getImportCandidateAction(),

            $this->getNextPageAction(),
        ];
    }

    protected function getNextPageAction(): Action
    {
        return Action::make(name: 'nextPage')
            ->authorize(abilities: 'preview')
            ->icon(icon: 'heroicon-s-chevron-double-right')
            ->label(label: __('filament.user.election-resource.pages.ballot_setup.actions.next.label'))
            ->outlined()
            ->url(url: Dashboard::getUrl(parameters: [$this->getElection()]));
    }

    protected function getCreatePositionAction(): CreateAction
    {
        return CreateAction::make(name: 'createPosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createPosition',
                    election: $livewire->getElection()
                )
            )
            ->createAnother(condition: false)
            ->form(
                form: fn (Form $form): Form => $form->schema(components: PositionResource::getFormComponents())
                    ->inlineLabel()
            )
            ->modelLabel(label: __('filament.user.position-resource.label'))
            ->pluralModelLabel(label: __('filament.user.position-resource.plural_label'))
            ->model(model: Position::class)
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalWidth(width: MaxWidth::Large)
            ->mutateFormDataUsing(callback: function (array $data): array {
                $data['threshold'] = $data['abstain'] ? $data['threshold'] : $data['quota'];

                return $data;
            })
            ->record(record: null)
            ->relationship(relationship: fn (HasElection $livewire) => $livewire->getElection()->positions())
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getReorderPositionAction(): EditAction
    {
        return EditAction::make(name: 'reorderPosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'reorderPosition',
                    election: $livewire->getElection()
                )
            )
            ->form(form: [
                Repeater::make(name: 'positions')
                    ->addable(condition: false)
                    ->deletable(condition: false)
                    ->hiddenLabel()
                    ->orderColumn()
                    ->relationship()
                    ->reorderable()
                    ->simple(field: TextInput::make(name: 'name')->disabled()),
            ])
            ->icon(icon: 'heroicon-m-arrows-up-down')
            ->iconButton()
            ->modalHeading(heading: 'Reorder Positions')
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getEditPositionAction(): InfolistAction
    {
        return InfolistAction::make(name: 'editPosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'updateAnyPosition',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (InfolistAction $action, Position $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();

                $action->success();
            })
            ->fillForm(
                data: fn (HasActions $livewire, Position $record): array => $livewire->makeFilamentTranslatableContentDriver()
                    ?->getRecordAttributesToArray($record)
                    ?? $record->attributesToArray()
            )
            ->form(
                form: fn (Form $form, Position $record): Form => PositionResource::form(form: $form)
                    ->inlineLabel()
                    ->model(model: $record)
            )
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalHeading(heading: fn (Position $record): string => "Edit $record->name")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->mutateFormDataUsing(callback: function (array $data): array {
                $data['threshold'] = $data['abstain'] ? $data['threshold'] : $data['quota'];

                return $data;
            })
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.edit_position.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getDeletePositionAction(): InfolistAction
    {
        return InfolistAction::make(name: 'deletePosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'deleteAnyPosition',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (InfolistAction $action, Position $record): void {
                $record->delete();

                $action->success();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Delete $record->name")
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.delete_position.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getReorderCandidateAction(): InfolistAction
    {
        return InfolistAction::make(name: 'reorderCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'reorderCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (InfolistAction $action, Position $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();

                $action->success();
            })
            ->fillForm(
                data: fn (HasActions $livewire, Position $record): array => $livewire->makeFilamentTranslatableContentDriver()
                    ?->getRecordAttributesToArray($record)
                    ?? $record->attributesToArray()
            )
            ->form(
                form: fn (Form $form, Position $record): Form => $form
                    ->model(model: $record)
                    ->schema(components: [
                        Repeater::make(name: 'candidates')
                            ->addable(condition: false)
                            ->deletable(condition: false)
                            ->hiddenLabel()
                            ->orderColumn()
                            ->relationship()
                            ->reorderable()
                            ->simple(
                                field: TextInput::make(name: 'display_name')
                                    ->disabled()
                            ),
                    ])
            )
            ->icon(icon: 'heroicon-m-arrows-up-down')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => __('filament.user.election-resource.pages.ballot_setup.actions.reorder_candidate.modal_heading', ['label' => $record->name]))
            ->modalSubmitActionLabel(label: __('filament.user.election-resource.pages.ballot_setup.actions.reorder_candidate.modal_actions.submit.label'))
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.reorder_candidate.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getImportCandidateAction(): ImportAction
    {
        return ImportAction::make(name: 'importCandidate')
            ->authorize(abilities: 'importCandidate')
            ->chunkSize(size: 25)
            ->icon(icon: 'heroicon-m-arrow-up-tray')
            ->importer(importer: CandidateImporter::class)
            ->label(label: __('filament.user.election-resource.pages.ballot_setup.actions.import_candidate.label'))
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->options(options: [
                'election_id' => $this->getElection()->getKey(),
                'locale' => app()->currentLocale(),
            ])
            ->visible(condition: fn (): bool => $this->canImportCandidate());
    }

    protected function getCreateCandidateAction(): InfolistAction
    {
        $pref = $this->getElection()->preference;

        return InfolistAction::make(name: 'createCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data, Form $form, array $arguments, InfolistAction $action): void {
                $candidate = new Candidate;
                $candidate->fill(attributes: $data);
                $record->candidates()->save(model: $candidate);

                $form->model($candidate)->saveRelationships();

                if ($arguments['another'] ?? false) {
                    $action->callAfter();
                    $action->sendSuccessNotification();

                    // Ensure that the form record is anonymized so that relationships aren't loaded.
                    $form->model(model: Candidate::class);

                    $form->fill();

                    $action->halt();
                }

                $action->success();
            })
            ->extraModalFooterActions(actions: fn (InfolistAction $action): array => [
                $action->makeModalSubmitAction(name: 'createAnother', arguments: ['another' => true])
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
            ])
            ->form(form: fn (Form $form, Position $record) => CandidateResource::form(form: $form, position: $record))
            ->icon(icon: 'heroicon-m-plus')
            ->label(label: __('filament.user.election-resource.pages.ballot_setup.actions.create_candidate.label'))
            ->modalHeading(heading: fn (Position $record): string => __('filament.user.election-resource.pages.ballot_setup.actions.create_candidate.modal_heading', ['position' => $record->name]))
            ->modalSubmitActionLabel(label: __('filament-actions::create.single.modal.actions.create.label'))
            ->modalWidth(width: match (true) {
                $pref->candidate_symbol && $pref->candidate_photo => MaxWidth::ThreeExtraLarge,
                $pref->candidate_symbol,
                $pref->candidate_photo => MaxWidth::TwoExtraLarge,
                default => MaxWidth::ExtraLarge,

            })
            ->outlined()
            ->size(size: ActionSize::Small)
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.create_candidate.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getEditCandidateAction(): InfolistAction
    {
        $pref = $this->getElection()->preference;

        return InfolistAction::make(name: 'editCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'updateAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Candidate $record, array $data, InfolistAction $action): void {
                $record->fill(attributes: $data);

                $record->save();

                $action->success();
            })
            ->fillForm(
                data: fn (HasActions $livewire, Candidate $record): array => $livewire->makeFilamentTranslatableContentDriver()
                    ?->getRecordAttributesToArray($record)
                    ?? $record->attributesToArray()
            )
            ->form(
                form: fn (Form $form, Candidate $record): Form => CandidateResource::form(form: $form, position: $record->position)
                    ->model(model: $record)
            )
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => __('filament.user.election-resource.pages.ballot_setup.actions.edit_candidate.modal_heading', ['label' => $record->full_name]))
            ->modalSubmitActionLabel(label: __('filament.user.election-resource.pages.ballot_setup.actions.edit_candidate.modal_actions.submit.label'))
            ->modalWidth(width: match (true) {
                $pref->candidate_symbol && $pref->candidate_photo => MaxWidth::ThreeExtraLarge,
                $pref->candidate_symbol,
                $pref->candidate_photo => MaxWidth::TwoExtraLarge,
                default => MaxWidth::ExtraLarge,

            })
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.edit_candidate.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getDeleteCandidateAction(): InfolistAction
    {
        return InfolistAction::make(name: 'deleteCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'deleteAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (InfolistAction $action, Candidate $record): void {
                $record->delete();

                $action->success();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => __('filament.user.election-resource.pages.ballot_setup.actions.delete_candidate.modal_heading', ['label' => $record->full_name]))
            ->successNotificationTitle(title: __('filament.user.election-resource.pages.ballot_setup.actions.delete_candidate.success_notification.title'))
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getDisableCandidateAction(): InfolistAction
    {
        return InfolistAction::make(name: 'disableCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'disableAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (InfolistAction $action, Candidate $record): void {
                $record->update(attributes: ['disabled' => true]);

                $action->success();
            })
            ->color(color: 'danger')
            ->hidden(condition: fn (Candidate $record): string => $record->disabled)
            ->icon(icon: 'heroicon-m-eye-slash')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => "Disable $record->display_name")
            ->successNotificationTitle(title: 'Disabled')
            ->visible(condition: $this->hasFullAccess());
    }

    protected function getEnableCandidateAction(): InfolistAction
    {
        return InfolistAction::make(name: 'enableCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'enableAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (InfolistAction $action, Candidate $record): void {
                $record->update(attributes: ['disabled' => false]);

                $action->success();
            })
            ->color(color: 'success')
            ->hidden(condition: fn (Candidate $record): string => ! $record->disabled)
            ->icon(icon: 'heroicon-m-eye')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => "Enable $record->display_name")
            ->successNotificationTitle(title: 'Enabled')
            ->visible(condition: $this->hasFullAccess());
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewBallotSetup', election: $election);
    }

    protected function canImportCandidate(): bool
    {
        return $this->hasFullAccess()
            && $this->getElection()->candidateImports()->whereNull('completed_at')->count() < 1;
    }
}
