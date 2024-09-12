<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QsyssMeetingRegistration extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'postal_code',
    ];
}
