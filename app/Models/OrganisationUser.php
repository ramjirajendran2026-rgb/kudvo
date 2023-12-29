<?php

namespace App\Models;

use App\Enums\OrganisationUserRoleEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganisationUser extends Pivot
{
    protected $table = 'organisation_user';

    protected $casts = [
        'role' => OrganisationUserRoleEnum::class,
    ];
}
