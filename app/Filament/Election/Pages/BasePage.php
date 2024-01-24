<?php

namespace App\Filament\Election\Pages;

use App\Facades\Kudvo;
use App\Filament\Contracts\HasElection;
use App\Filament\Contracts\HasElector;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Models\Election;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Auth\Access\AuthorizationException;
use function Filament\authorize;

/**
 * @property Form $form
 */
abstract class BasePage extends Page implements HasElector, HasElection
{
    use InteractsWithElection;

    public static function can(string $action)
    {
        try {
            return authorize(action: $action, model: Kudvo::getElection() ?? Election::class)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }
}
