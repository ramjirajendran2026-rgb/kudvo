<?php

namespace App\Filament\Nomination\Pages;

use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use App\Filament\Nomination\Pages\Contracts\HasElector;
use App\Filament\Nomination\Pages\Contracts\HasNomination;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * @property Form $form
 */
abstract class BasePage extends Page implements HasElector, HasNomination
{
    use InteractsWithNomination;

    protected ?string $maxContentWidth = '3xl';
}
