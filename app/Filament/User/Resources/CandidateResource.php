<?php

namespace App\Filament\User\Resources;

use App\Filament\Contracts\HasElection;
use App\Forms\CandidateForm;
use App\Models\Candidate;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Guava\FilamentClusters\Forms\Cluster;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->model(model: static::getModel())
            ->schema(components: static::getFormComponents());
    }

    public static function getFormComponents(): array
    {
        return [
            Split::make(schema: [
                CandidateForm::photoComponent()
                    ->grow(condition: false)
                    ->hiddenLabel()
                    ->maxWidth(width: 200)
                    ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_photo),

                Group::make()
                    ->columns()
                    ->schema(components: [
                        CandidateForm::membershipNumberComponent()
                            ->hiddenLabel()
                            ->live(onBlur: true)
                            ->columnSpanFull(),

                        Cluster::make(schema: [
                            CandidateForm::titleComponent()
                                ->placeholder(placeholder: 'Title'),

                            CandidateForm::firstNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: 'First name'),

                            CandidateForm::lastNameComponent()
                                ->columnSpan(2)
                                ->placeholder(placeholder: 'Last name'),
                        ])
                            ->columns(columns: 5)
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->label(label: 'Full name'),

                        CandidateForm::emailComponent()
                            ->hiddenLabel(),

                        CandidateForm::phoneComponent()
                            ->defaultCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country'))
                            ->disableIpLookUp()
                            ->hiddenLabel()
                            ->initialCountry(value: Filament::getTenant()?->country ?: config(key: 'app.default_phone_country')),
                    ]),

                CandidateForm::symbolComponent()
                    ->grow(condition: false)
                    ->hiddenLabel()
                    ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_symbol),
            ]),
        ];
    }
}
