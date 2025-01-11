<?php

namespace App\Filament\Base\Contracts;

use App\Models\Meeting;

interface ResolvesMeeting
{
    public function resolveMeeting(string $key): Meeting;
}
