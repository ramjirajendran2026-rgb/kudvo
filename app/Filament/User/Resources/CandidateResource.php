<?php

namespace App\Filament\User\Resources;

use App\Filament\Base\Contracts\HasElection;
use App\Forms\CandidateForm;
use App\Models\Candidate;
use App\Models\Position;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Validation\Rules\Unique;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    public static function form(Form $form, ?Position $position = null): Form
    {
        return $form
            ->columns(null)
            ->model(model: static::getModel())
            ->schema(components: static::getFormComponents(position: $position));
    }

    public static function getFormComponents(?Position $position = null): array
    {
        return [
            Split::make(schema: [
                CandidateForm::photoComponent()
                    ->grow(condition: false)
                    ->hiddenLabel()
                    ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_photo),

                Group::make()
                    ->columns()
                    ->schema(components: [
                        CandidateForm::membershipNumberComponent()
                            ->hiddenLabel()
                            ->live(onBlur: true)
                            ->columnSpanFull()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->where(column: 'position_id', value: $position?->getKey())
                            ),

                        Cluster::make(schema: [
                            CandidateForm::titleComponent(),

                            CandidateForm::firstNameComponent()
                                ->columnSpan(2),

                            CandidateForm::lastNameComponent()
                                ->columnSpan(2),
                        ])
                            ->columns(columns: 5)
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->label(label: __('filament.user.candidate-resource.form.full_name.label')),

                        CandidateForm::candidateGroupIdComponent()
                            ->columnSpanFull()
                            ->hiddenLabel(),

                        CandidateForm::emailComponent()
                            ->hidden()
                            ->hiddenLabel(),

                        CandidateForm::phoneComponent()
                            ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                            ->disableIpLookUp()
                            ->hidden()
                            ->hiddenLabel()
                            ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),
                    ]),

                CandidateForm::symbolComponent()
                    ->grow(condition: false)
                    ->hiddenLabel()
                    ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_symbol),
            ])
                ->from(breakpoint: 'md'),
        ];
    }
}
