<?php

namespace App\Filament\User\Resources;

use App\Forms\CandidateForm;
use App\Models\Candidate;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    public static function form(Form $form): Form
    {
        return $form
            ->columns()
            ->model(model: static::getModel())
            ->schema(components: static::getFormComponents());
    }

    public static function getFormComponents(): array
    {
        return [
            CandidateForm::membershipNumberComponent()
                ->columnSpanFull(),

            CandidateForm::firstNameComponent(),

            CandidateForm::lastNameComponent(),

            CandidateForm::emailComponent(),

            CandidateForm::phoneComponent(),
        ];
    }
}
