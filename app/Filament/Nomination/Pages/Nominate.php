<?php

namespace App\Filament\Nomination\Pages;

use App\Console\NominatorStatusEnum;
use App\Enums\NomineeStatusEnum;
use App\Filament\Forms\ElectorForm;
use App\Filament\Forms\NominatorForm;
use App\Filament\Forms\NomineeForm;
use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Nominee;
use App\Models\Position;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Exists;

class Nominate extends NominationPage
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.nomination.pages.nominate';

    protected ?string $subheading = 'Nomination Form';

    public ?array $data = [];

    public function mount(): void
    {
        $nomineeData = [
            ...$this->getNomination()->self_nomination ?
                $this->getElector()->only(attributes: ['membership_number', 'first_name', 'last_name', 'email', 'phone']) :
                [],

            'nominators' => [
                ...($this->getNomination()->self_nomination && $this->getNomination()->nominator_threshold) ?
                    [[]] :
                    [$this->getElector()->only(attributes: ['membership_number', 'first_name', 'last_name', 'email', 'phone'])],

                ...$this->getNomination()->nominator_threshold > 1 ?
                    Arr::map(array: range(start: 2, end: $this->getNomination()->nominator_threshold), callback: fn () => []) :
                    []
            ],
        ];

        $this->form->fill(state: $nomineeData);
    }

    public function submit()
    {
        $data = $this->form->getState();

        $nominee = Nominee::create([
            'self_nomination' => $this->getNomination()->self_nomination,
            'status' => $this->getNomination()->self_nomination ? NomineeStatusEnum::NOMINATED : NomineeStatusEnum::PROPOSED,

            ...$data
        ]);

        $this->form->model(model: $nominee)->saveRelationships();

        dd($this->form->getRecord());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->compact()
                    ->schema(components: [
                        NomineeForm::positionIdComponent(),
                    ]),

                Section::make(heading: 'Nominee')
                    ->columns()
                    ->compact()
                    ->schema(components: [
                        NomineeForm::membershipNumberComponent()
                            ->columnSpanFull(),

                        NomineeForm::firstNameComponent()
                            ->readOnly(),

                        NomineeForm::lastNameComponent()
                            ->readOnly(),

                        NomineeForm::emailComponent()
                            ->readOnly(),

                        NomineeForm::phoneComponent(),
                    ]),

                Repeater::make(name: 'nominators')
                    ->addable(condition: false)
                    ->columns()
                    ->deletable(condition: false)
                    ->hiddenLabel()
                    ->itemLabel(
                        label: function (Get $get, string $uuid): ?string {
                            $index = array_search(needle: $uuid, haystack: array_keys($get(path: 'nominators')));

                            return $index ? 'Seconder #'.$index : 'Proposer';
                        }
                    )
                    ->maxItems(count: $this->getNomination()->nominator_threshold)
                    ->minItems(count: $this->getNomination()->nominator_threshold)
                    ->mutateRelationshipDataBeforeCreateUsing(callback: function (array $data): array {
                        $data['status'] = NominatorStatusEnum::PENDING;

                        return $data;
                    })
                    ->relationship()
                    ->schema(components: [
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
            $this->getSubmitAction()
        ];
    }

    protected function getSubmitAction()
    {
        return Action::make(name: 'submit')
            ->submit(form: 'submit');
    }
}
