<?php

namespace App\Facades;

use App\KudvoManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see KudvoManager
 */
class Kudvo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kudvo';
    }
}
