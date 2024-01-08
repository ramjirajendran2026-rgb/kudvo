<?php

namespace App\Events;

use App\Models\Nominee;
use Illuminate\Foundation\Events\Dispatchable;

class NomineeNominated
{
    use Dispatchable;

    public function __construct(protected Nominee $nominee)
    {
    }

    public function getNominee(): Nominee
    {
        return $this->nominee;
    }
}
