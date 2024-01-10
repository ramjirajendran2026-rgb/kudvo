<?php

namespace App\Events;

use App\Models\Nominator;
use Illuminate\Foundation\Events\Dispatchable;

class NominatorAccepted
{
    use Dispatchable;

    public function __construct(protected Nominator $nominator)
    {
    }

    public function getNominator(): Nominator
    {
        return $this->nominator;
    }
}
