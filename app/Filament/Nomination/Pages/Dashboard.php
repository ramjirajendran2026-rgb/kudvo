<?php

namespace App\Filament\Nomination\Pages;

use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use App\Models\Nominee;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;

class Dashboard extends \Filament\Pages\Dashboard
{
    use InteractsWithNomination;

    protected function getNominateAction()
    {
        return Action::make(name: 'nominate')
            ->model(model: Nominee::class)
            ->form(form: [
                Select::make(name: 'position_id')
                    ->relationship(name: 'position'),
            ]);
    }
}
