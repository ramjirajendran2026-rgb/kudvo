<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\User\Resources\CandidateResource;
use App\Filament\User\Resources\PositionResource;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(components: [
                RepeatableEntry::make(name: 'positions')
                    ->contained(condition: false)
                    ->extraAttributes(attributes: ['class' => 'position-repeatable-entry'])
                    ->hiddenLabel()
                    ->placeholder(placeholder: fn () => $this->generateEmptyStatePlaceholder(
                        heading: 'No positions',
                        icon: 'heroicon-o-x-mark',
                        actions: [$this->getCreatePositionAction]
                    ))
                    ->schema(components: [
                        Section::make(heading: fn (Position $state): ?string => $state->name)
                            ->compact()
                            ->description(
                                description: fn (Position $state): ?string => Str::plural(value: $state->quota.' Post', count: $state->quota).
                                    ($state->abstain ? Str::plural(value: " • Minimum $state->threshold selection", count: $state->threshold) : '')
                            )
                            ->headerActions(actions: [
                                $this->getCreateCandidateAction(),

                                $this->getReorderCandidateAction(),

                                $this->getEditPositionAction(),

                                $this->getDeletePositionAction(),
                            ])
                            ->schema(components: [
                                RepeatableEntry::make(name: 'candidates')
                                    ->extraAttributes(attributes: ['class' => 'candidate-repeatable-entry'])
                                    ->hiddenLabel()
                                    ->placeholder(placeholder: $this->generateEmptyStatePlaceholder(
                                        heading: 'No candidates',
                                        description: 'Create new candidate',
                                        icon: 'heroicon-o-x-mark',
                                    ))
                                    ->schema(components: [
                                        Split::make(schema: [
                                            SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                ->circular()
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->photo_url)
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80)
                                                ->visible(condition: $this->getElection()->preference?->candidate_photo),

                                            Group::make()
                                                ->schema(components: [
                                                    TextEntry::make(name: 'display_name')
                                                        ->hiddenLabel()
                                                        ->size(size: TextEntry\TextEntrySize::Large)
                                                        ->suffixActions(actions: [
                                                            $this->getEditCandidateAction(),

                                                            $this->getDeleteCandidateAction(),
                                                        ])
                                                        ->weight(weight: FontWeight::Medium),

                                                    TextEntry::make(name: 'membership_number')
                                                        ->color(color: 'gray')
                                                        ->getStateUsing(
                                                            callback: fn (Candidate $record): ?string => collect(value: [
                                                                $record->membership_number,
                                                                $record->email,
                                                                $record->phone
                                                            ])->filter(callback: fn (?string $item): bool => filled(value: $item))->implode(value: ' • ')
                                                        )
                                                        ->hiddenLabel()
                                                        ->visible(condition: fn (?string $state): bool => filled($state)),
                                                ]),

                                            SpatieMediaLibraryImageEntry::make(name: 'symbol')
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_SYMBOL)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => $record->symbol_url)
                                                ->extraImgAttributes(attributes: ['class' => 'rounded-xl'])
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80)
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
            ->record($this->getElection());
    }

    protected function generateEmptyStatePlaceholder(string $heading, ?string $description = null, ?string $icon = null, array $actions = []): HtmlString
    {
        return new HtmlString(
            html: Blade::render(
                string: <<<'HTML'
<x-filament::section>
    <x-filament.state
        :heading="$heading"
        :description="$description"
        :icon="$icon"
        :actions="$actions"
    />
</x-filament::section>
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

    protected function getHeaderActions(): array
    {
        return [
            $this->getReorderPositionAction(),

            $this->getCreatePositionAction(),

            $this->getPreviewBallotAction(),

            $this->getNextPageAction(),
        ];
    }

    protected function getNextPageAction(): Action
    {
        return Action::make(name: 'nextPage')
            ->authorize(abilities: 'preview')
            ->icon(icon: 'heroicon-s-chevron-double-right')
            ->label(label: 'Next')
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
            ->form(form: fn (Form $form): Form => $form->schema(components: PositionResource::getFormComponents()))
            ->model(model: Position::class)
            ->modalWidth(width: MaxWidth::Large)
            ->mutateFormDataUsing(callback: function (array $data): array {
                $data['threshold'] = $data['abstain'] ? $data['threshold'] : $data['quota'];

                return $data;
            })
            ->record(record: null)
            ->relationship(relationship: fn(HasElection $livewire) => $livewire->getElection()->positions());
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
                    ->reorderable() // TODO: Bug in filament
                    ->simple(field: TextInput::make(name: 'name')->disabled()),
            ])
            ->icon(icon: 'heroicon-m-arrows-up-down')
            ->iconButton()
            ->modalHeading(heading: 'Reorder Positions')
            ->modalWidth(width: MaxWidth::ExtraLarge);
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
            ->fillForm(data: fn (Position $record): array => $record->attributesToArray())
            ->form(form: fn (Form $form): Form => PositionResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Edit $record->name")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->mutateFormDataUsing(callback: function (array $data): array {
                $data['threshold'] = $data['abstain'] ? $data['threshold'] : $data['quota'];

                return $data;
            })
            ->successNotificationTitle(title: 'Saved');
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
            ->successNotificationTitle(title: 'Deleted');
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
            ->fillForm(data: fn (Position $record): array => $record->attributesToArray())
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
                            ->reorderable() // TODO: Bug in filament
                            ->simple(
                                field: TextInput::make(name: 'display_name')
                                    ->disabled()
                            ),
                    ])
            )
            ->icon(icon: 'heroicon-m-arrows-up-down')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Reorder $record->name Candidates")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->successNotificationTitle(title: 'Saved');
    }

    protected function getCreateCandidateAction(): InfolistAction
    {
        return InfolistAction::make(name: 'createCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data, Form $form, array $arguments, InfolistAction $action): void {
                $candidate = new Candidate();
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
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label'))
            ])
            ->form(form: fn (Form $form, Position $record) => CandidateResource::form(form: $form, position: $record))
            ->icon(icon: 'heroicon-m-plus')
            ->label(label: 'New candidate')
            ->modalSubmitActionLabel(label: __('filament-actions::create.single.modal.actions.create.label'))
            ->outlined()
            ->size(size: ActionSize::Small)
            ->successNotificationTitle(title: 'Created');
    }

    protected function getEditCandidateAction(): InfolistAction
    {
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
            ->fillForm(data: fn (Candidate $record): array => $record->attributesToArray())
            ->form(
                form: fn (Form $form, Candidate $record): Form => CandidateResource::form(form: $form, position: $record->position)
                    ->model(model: $record)
            )
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => "Edit $record->full_name")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->successNotificationTitle(title: 'Saved');
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
            ->modalHeading(heading: fn (Candidate $record): string => "Delete $record->full_name")
            ->successNotificationTitle(title: 'Deleted');
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewBallotSetup', election: $election);
    }
}
