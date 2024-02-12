<?php

namespace App\Filament\User\Resources;

use App\Filament\Contracts\HasElection;
use App\Forms\CandidateForm;
use App\Models\Candidate;
use Filament\Forms\Components\Group;
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
            Group::make()
                ->columns(columns: 5)
                ->schema(components: [
                    Group::make()
                        ->columns()
                        ->columnSpan(span: 4)
                        ->schema(components: [
                            CandidateForm::membershipNumberComponent()
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
                                ->label(label: 'Full name'),

                            CandidateForm::emailComponent(),

                            CandidateForm::phoneComponent(),
                        ]),

                    CandidateForm::photoComponent()
                        ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->candidate_photo),
                ]),
        ];
    }
}
