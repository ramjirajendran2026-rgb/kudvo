<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\User\Resources\CandidateResource;
use App\Filament\User\Resources\PositionResource;
use App\Forms\CandidateForm;
use App\Forms\Components\VotePicker;
use App\Forms\PositionForm;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
                    ->placeholder(placeholder: 'No positions')
                    ->schema(components: [
                        Section::make(heading: fn (Position $state): ?string => $state->name)
                            ->compact()
                            ->description(description: fn (Position $state): ?string => $state->quota.Str::plural(value: ' Post', count: $state->quota))
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
                                    ->placeholder(placeholder: 'No candidates')
                                    ->schema(components: [
                                        Split::make(schema: [
                                            SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                ->circular()
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => filament()->getUserAvatarUrl($record))
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80),

                                            Group::make()
                                                ->schema(components: [
                                                    TextEntry::make(name: 'full_name')
                                                        ->hiddenLabel()
                                                        ->size(size: TextEntry\TextEntrySize::Large)
                                                        ->suffixActions(actions: [
                                                            $this->getEditCandidateAction(),

                                                            $this->getDeleteCandidateAction(),
                                                        ]),

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

    protected function getHeaderActions(): array
    {
        return [
            $this->getPreviewBallotAction(),

            $this->getCreatePositionAction(),

            $this->getReorderPositionAction(),

            ...parent::getHeaderActions(),
        ];
    }

    protected function getPreviewBallotAction()
    {
        return Action::make(name: 'previewBallot')
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
                $data['votes'] = Arr::mapWithKeys($data['votes'], fn ($item, $key) => [$key => Arr::map($item, fn ($subItem) => $subItem['key'])]);

                $form->fill(state: $data);

                $action->formData(data: $data);
                $action->halt();
            })
            ->color(color: 'success')
            ->form(
                form: fn (self $livewire): array => [
                    Hidden::make(name: 'preview')
                        ->default(state: false),

                    \Filament\Forms\Components\Group::make(
                        schema: $livewire->getElection()->positions
                            ->map(
                                callback: fn (Position $position) => VotePicker::makeFor(position: $position)
                                    ->disabled(condition: fn (Get $get): bool => $get(path: '../preview'))
                                    ->preview(condition: fn (Get $get): bool => $get(path: '../preview')),
                            )
                            ->toArray()
                    )
                    ->statePath(path: 'votes')
                ]
            )
            ->icon(icon: 'heroicon-m-eye')
//            ->iconButton()
            ->label(label: 'Preview')
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalDescription(description: $this->getSubheading())
            ->modalHeading(heading: $this->getHeading())
            ->modalCancelAction(action: false)
            ->modalSubmitActionLabel(label: fn (array $data): string => ($data['preview'] ?? false) ? 'Confirm' : 'Continue')
            ->slideOver();
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
            ->form(form: fn (Form $form): Form => $form->schema(components: [
                ...PositionResource::getFormComponents(),

                Repeater::make(name: 'candidates')
                    ->addActionLabel(label: 'Add another candidate')
                    ->defaultItems(count: 2)
                    ->itemLabel(label: fn (string $uuid, Repeater $component): string => 'Candidate #'.(array_search($uuid, array_keys($component->getState())))+1)
                    ->orderColumn()
                    ->reorderable()
                    ->relationship()
                    ->rule(rule: fn (Get $get): string => 'min:'.($get(path: 'threshold') ?? $get(path: 'quota')))
                    ->schema(components: CandidateResource::getFormComponents())
                    ->visible(condition: false),
            ]))
            ->modalWidth(width: MaxWidth::Large)
            ->model(model: Position::class)
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
                    ->relationship()
                    ->orderColumn()
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
            ->action(action: function (Position $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();
            })
            ->fillForm(data: fn (Position $record): array => $record->attributesToArray())
            ->form(form: fn (Form $form): Form => PositionResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Edit $record->name")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->modalWidth(width: MaxWidth::ExtraLarge);
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
            ->action(action: function (Position $record): void {
                $record->delete();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Delete $record->name");
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
            ->action(action: function (Position $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();
            })
            ->fillForm(data: fn (Position $record): array => $record->attributesToArray())
            ->form(
                form: fn (Form $form, Position $record): Form => $form
                    ->model(model: $record)
                    ->schema(components: [
                        Repeater::make(name: 'candidates')
                            ->addable(condition: false)
                            ->deletable(condition: false)
                            ->relationship()
                            ->orderColumn()
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
            ->modalWidth(width: MaxWidth::ExtraLarge);
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
            ->action(action: function (Position $record, array $data, Form $form): void {
                $candidate = new Candidate();
                $candidate->fill(attributes: $data);
                $record->candidates()->save(model: $candidate);

                $form->model($candidate)->saveRelationships();
            })
            ->form(form: fn (Form $form) => CandidateResource::form(form: $form))
            ->icon(icon: 'heroicon-m-plus')
            ->label(label: 'New candidate')
            ->outlined()
            ->size(size: ActionSize::Small);
    }

    protected function getCreateCandidatesAction(): InfolistAction
    {
        return InfolistAction::make(name: 'createCandidates')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createCandidates',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data): void {
                foreach ($data['candidates'] as $candidate) {
                    $record->candidates()->create(attributes: $candidate);
                }
            })
            ->form(form: [
                Repeater::make(name: 'candidates')
                    ->defaultItems(1)
                    ->schema(components: CandidateResource::getFormComponents()),
            ])
            ->icon(icon: 'heroicon-m-plus')
            ->iconButton();
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
            ->action(action: function (Candidate $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();
            })
            ->fillForm(data: fn (Candidate $record): array => $record->attributesToArray())
            ->form(
                form: fn (Form $form, Candidate $record): Form => CandidateResource::form(form: $form)
                    ->model(model: $record)
            )
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalHeading(heading: fn (Candidate $record): string => "Edit $record->full_name")
            ->modalSubmitActionLabel(label: 'Save changes');
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
            ->action(action: function (Candidate $record): void {
                $record->delete();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => "Delete $record->full_name");
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewBallotSetup', election: $election);
    }
}
