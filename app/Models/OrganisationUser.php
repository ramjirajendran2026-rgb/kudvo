<?php

namespace App\Models;

use App\Enums\OrganisationUserRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganisationUser extends Pivot
{
    protected $table = 'organisation_user';

    protected $casts = [
        'role' => OrganisationUserRole::class,
    ];
}
