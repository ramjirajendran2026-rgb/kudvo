<?php

namespace App\Events;

use App\Models\Nominator;
use Illuminate\Foundation\Events\Dispatchable;

class NominatorDeclined
{
    use Dispatchable;

    public function __construct(Nominator $nominator)
    {
    }
}
