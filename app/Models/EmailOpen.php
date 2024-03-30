<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOpen extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'opened_at',
        'email_id',
    ];

    protected $casts = [
        'opened_at' => 'immutable_datetime',
        'email_id' => 'int',
    ];
}
