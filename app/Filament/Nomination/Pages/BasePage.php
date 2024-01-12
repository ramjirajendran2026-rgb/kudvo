<?php

namespace App\Filament\Nomination\Pages;

use App\Filament\Contracts\HasElector;
use App\Filament\Contracts\HasNomination;
use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * @property Form $form
 */
abstract class BasePage extends Page implements HasElector, HasNomination
{
    use InteractsWithNomination;
}
