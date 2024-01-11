<?php

namespace App\Filament\User\Contracts;

interface HasElectorGroups
{
    public function getElectorGroups(): array;
}
