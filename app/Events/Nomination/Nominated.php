<?php

namespace App\Events\Nomination;

use App\Models\Nominee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Nominated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        protected Nominee $nominee
    )
    {
    }

    public function getNominee(): Nominee
    {
        return $this->nominee;
    }
}
