<?php

namespace App\Filament\Election\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\Contracts\HasElector;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * @property Form $form
 */
abstract class BasePage extends Page implements HasElector, HasElection
{
    use InteractsWithElection;
}
