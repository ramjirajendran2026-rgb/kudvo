<?php

namespace App\Events;

use App\Models\Nominee;
use Illuminate\Foundation\Events\Dispatchable;

class NomineeAccepted
{
    use Dispatchable;

    public function __construct(Nominee $nominee)
    {
    }
}
