<?php

namespace App\Filament\Nomination\Pages;

use App\Enums\NominatorStatus;
use App\Enums\NomineeStatus;
use App\Events\Nomination\Nominated;
use App\Filament\Forms\NominatorForm;
use App\Filament\Forms\NomineeForm;
use App\Models\Nominee;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Nominate extends BasePage
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.nomination.pages.nominate';

    protected static bool $isDiscovered = false;

    protected ?string $subheading = 'Nomination Form';

    public ?array $data = [];

    public function mount(): void
    {
        $electorData = [
            'elector_id' => $this->getElector()->getKey(),
            ...$this->getElector()->only(attributes: ['membership_number', 'first_name', 'last_name', 'email', 'phone'])
        ];

        $nomineeData = [
            ...$this->getNomination()->self_nomination ?
                $electorData :
                [],

            'nominators' => $this->getNomination()->nominator_threshold ? [
                ...($this->getNomination()->self_nomination) ?
                    [[]] :
                    [$electorData],

                ...$this->getNomination()->nominator_threshold > 1 ?
                    Arr::map(array: range(start: 2, end: $this->getNomination()->nominator_threshold), callback: fn () => []) :
                    []
            ] :
            [],
        ];

        $this->form->fill(state: $nomineeData);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $nominee = Nominee::create([
            'self_nomination' => $this->getNomination()->self_nomination,

            ...$data
        ]);

        $this->form->model(model: $nominee)->saveRelationships();

        Nominated::dispatch($nominee);

        Notification::make()
            ->success()
            ->title(title: 'Nominated')
            ->send();

        $this->redirect(Filament::getCurrentPanel()->getUrl());
    }

    public function form(Form $form): Form
    {
        $isSelfNomination = $this->getNomination()->self_nomination;
        $nominatorThreshold = $this->getNomination()->nominator_threshold;
        $seconderThreshold = $nominatorThreshold - 1;

        return $form
            ->schema(components: [
                Wizard::make()
                    ->submitAction(action: $this->getSubmitAction)
                    ->steps(steps: [
                        Wizard\Step::make(label: 'Nominee')
                            ->description(description: $isSelfNomination ? 'You' : null)
                            ->schema(components: [
                                NomineeForm::positionIdComponent()
                                    ->hiddenLabel(condition: false),

                                NomineeForm::electorIdComponent(),

                                NomineeForm::membershipNumberComponent(),

                                Group::make()
                                    ->columns(columns: 5)
                                    ->schema(components: [
                                        Group::make()
                                            ->columns()
                                            ->columnSpan(span: $isSelfNomination ? 4 : 'full')
                                            ->schema(components: [
                                                NomineeForm::firstNameComponent()
                                                    ->readOnly(),

                                                NomineeForm::lastNameComponent()
                                                    ->readOnly(),

                                                NomineeForm::emailComponent()
                                                    ->readOnly(),

                                                NomineeForm::phoneComponent(),
                                            ]),

                                        NomineeForm::photoComponent()
                                            ->visible(condition: $isSelfNomination),
                                    ]),

                                NomineeForm::bioComponent()
                                    ->visible(condition: $isSelfNomination),

                                NomineeForm::attachmentComponent()
                                    ->visible(condition: $isSelfNomination),
                            ]),

                        Wizard\Step::make(label: Str::plural(value: 'Nominator', count: $nominatorThreshold))
                            ->description(
                                description: ($isSelfNomination ? 'Proposer' : 'You').
                                (
                                $seconderThreshold ?
                                    (" and ".Str::plural(value: 'Seconder', count: $seconderThreshold)) :
                                    ''
                                )
                            )
                            ->visible(condition: $nominatorThreshold)
                            ->schema(components: [
                                Repeater::make(name: 'nominators')
                                    ->addable(condition: false)
                                    ->collapsible()
                                    ->collapseAllAction(callback: fn (FormAction $action): FormAction => $action->hidden())
                                    ->columns()
                                    ->deletable(condition: false)
                                    ->expandAllAction(callback: fn (FormAction $action): FormAction => $action->hidden())
                                    ->hiddenLabel()
                                    ->itemLabel(
                                        label: function (Get $get, string $uuid, self $livewire): ?string {
                                            $uuids = array_keys($get(path: 'nominators'));
                                            $index = array_search(needle: $uuid, haystack: $uuids);

                                            return $index ?
                                                'Seconder '.(count(value: $uuids) > 2 ? '#'.$index : '') :
                                                'Proposer'.(!$livewire->getNomination()->self_nomination ? ' (You)' : '');
                                        }
                                    )
                                    ->maxItems(count: $nominatorThreshold)
                                    ->minItems(count: $nominatorThreshold)
                                    ->mutateRelationshipDataBeforeCreateUsing(callback: function (array $data, self $livewire): array {
                                        if (($data['membership_number'] ?? null) == $livewire->getElector()->membership_number) {
                                            $data['status'] = NominatorStatus::ACCEPTED;
                                            $data['decided_at'] = Carbon::now();
                                        } else {
                                            $data['status'] = NominatorStatus::PENDING;
                                        }

                                        return $data;
                                    })
                                    ->relationship()
                                    ->schema(components: [
                                        NominatorForm::electorIdComponent(),

                                        NominatorForm::membershipNumberComponent()
                                            ->columnSpanFull(),

                                        NominatorForm::firstNameComponent()
                                            ->readOnly(),

                                        NominatorForm::lastNameComponent()
                                            ->readOnly(),

                                        NominatorForm::emailComponent()
                                            ->readOnly(),

                                        NominatorForm::phoneComponent(),
                                    ]),
                            ]),

                        Wizard\Step::make(label: 'Confirmation')
                            ->schema(components: [
                                Checkbox::make(name: 'consent')
                                    ->accepted()
                                    ->dehydrated(condition: false)
                                    ->label(label: 'I agree for the nomination')
                                    ->validationAttribute(label: 'consent'),
                            ]),
                    ]),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                form: $this->makeForm()
                    ->model(model: Nominee::class)
                    ->operation(operation: 'create')
                    ->statePath(path: 'data')
            ),
        ];
    }

    public function getFormActions(): array
    {
        return [
        ];
    }

    protected function getSubmitAction()
    {
        return Action::make(name: 'submit')
            ->submit(form: 'submit');
    }
}
