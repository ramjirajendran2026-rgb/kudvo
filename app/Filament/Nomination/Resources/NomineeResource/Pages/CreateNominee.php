<?php

namespace App\Filament\Nomination\Resources\NomineeResource\Pages;

use App\Enums\NominatorStatus;
use App\Events\NomineeNominated;
use App\Facades\Kudvo;
use App\Filament\Base\Contracts\HasElector;
use App\Filament\Base\Contracts\HasNomination;
use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use App\Filament\Nomination\Resources\NomineeResource;
use App\Forms\NominatorForm;
use App\Forms\NomineeForm;
use App\Models\Nominee;
use App\Models\Position;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @method Nominee|null getRecord()
 */
class CreateNominee extends CreateRecord implements HasElector, HasNomination
{
    use CreateRecord\Concerns\HasWizard;
    use InteractsWithNomination;

    protected static string $resource = NomineeResource::class;

    public function fillForm(): void
    {
        $electorData = [
            'elector_id' => $this->getElector()->getKey(),
            ...$this->getElector()->only(attributes: ['membership_number', 'first_name', 'last_name', 'email', 'phone']),
        ];

        $data = [
            ...$this->getNomination()->self_nomination ?
                $electorData :
                [],

            'nominators' => $this->getNomination()->nominator_threshold ? [
                ...($this->getNomination()->self_nomination) ?
                    [[]] :
                    [$electorData],

                ...$this->getNomination()->nominator_threshold > 1 ?
                    Arr::map(array: range(start: 2, end: $this->getNomination()->nominator_threshold), callback: fn () => []) :
                    [],
            ] :
                [],
        ];

        $this->form->fill(state: $data);
    }

    public function getHeading(): string | Htmlable
    {
        return Kudvo::getNomination()->name;
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Nomination Form';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Submitted';
    }

    public function getSteps(): array
    {
        $isSelfNomination = $this->getNomination()->self_nomination;
        $preference = $this->getNomination()->preference;
        $nominatorThreshold = $this->getNomination()->nominator_threshold;
        $seconderThreshold = $nominatorThreshold - 1;

        return [
            Step::make(label: 'Nominee')
                ->description(description: $isSelfNomination ? 'You' : null)
                ->schema(components: [
                    NomineeForm::positionIdComponent()
                        ->getOptionLabelFromRecordUsing(fn (Position $record) => $record->name)
                        ->hiddenLabel(condition: false)
                        ->relationship(
                            name: 'position',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, self $livewire): Builder => $query
                                ->whereMorphedTo(
                                    relation: 'event',
                                    model: $livewire->getNomination(),
                                )
                        ),

                    NomineeForm::electorIdComponent(),

                    NomineeForm::membershipNumberComponent(),

                    Group::make()
                        ->columns(columns: 5)
                        ->schema(components: [
                            Group::make()
                                ->columns()
                                ->columnSpan(span: $isSelfNomination && $preference->candidate_photo ? 4 : 'full')
                                ->schema(components: [
                                    NomineeForm::firstNameComponent()
                                        ->readOnly(),

                                    NomineeForm::lastNameComponent()
                                        ->readOnly(),

                                    NomineeForm::emailComponent()
                                        ->readOnly(),

                                    NomineeForm::phoneComponent()
                                        ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                                        ->disableLookup()
                                        ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),
                                ]),

                            NomineeForm::photoComponent()
                                ->required()
                                ->visible(condition: $isSelfNomination && $preference->candidate_photo),
                        ]),

                    NomineeForm::bioComponent()
                        ->required()
                        ->visible(condition: $isSelfNomination && $preference->candidate_bio),

                    NomineeForm::attachmentComponent()
                        ->required()
                        ->visible(condition: $isSelfNomination && $preference->candidate_attachment),
                ]),

            Step::make(label: Str::plural(value: 'Nominator', count: $nominatorThreshold))
                ->description(
                    description: ($isSelfNomination ? 'Proposer' : 'You') .
                    (
                        $seconderThreshold ?
                        (' and ' . Str::plural(value: 'Seconder', count: $seconderThreshold)) :
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
                                    'Seconder ' . (count(value: $uuids) > 2 ? '#' . $index : '') :
                                    'Proposer' . (! $livewire->getNomination()->self_nomination ? ' (You)' : '');
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

                            NominatorForm::phoneComponent()
                                ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                                ->disableLookup()
                                ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),
                        ]),
                ]),

            Step::make(label: 'Confirmation')
                ->schema(components: [
                    Checkbox::make(name: 'consent')
                        ->accepted()
                        ->dehydrated(condition: false)
                        ->label(label: 'I agree for the nomination')
                        ->validationAttribute(label: 'consent'),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            ...$data,

            'self_nomination' => $this->getNomination()->self_nomination,
        ];
    }

    protected function getSubmitFormAction(): Actions\Action
    {
        return parent::getSubmitFormAction()
            ->label(label: 'Submit');
    }

    protected function afterCreate(): void
    {
        $nominee = $this->getRecord()->fresh();

        NomineeNominated::dispatch($nominee);

        if ($nominee->self_nomination) {
            $nominee->accept();
        }
    }

    protected function isSelfNomination()
    {
        return $this->getNomination()->self_nomination;
    }
}
