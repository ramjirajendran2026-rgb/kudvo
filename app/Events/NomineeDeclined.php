<?php

namespace App\Events;

use App\Models\Nominee;
use Illuminate\Foundation\Events\Dispatchable;

class NomineeDeclined
{
    use Dispatchable;

    public function __construct(Nominee $nominee)
    {
    }
}
