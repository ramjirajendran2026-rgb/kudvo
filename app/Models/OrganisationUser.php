<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganisationUser extends Pivot
{
    protected $table = 'organisation_user';
}
