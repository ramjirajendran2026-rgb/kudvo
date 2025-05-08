<?php

namespace App\Models;

use App\Enums\SmsChannel;
use Illuminate\Database\Eloquent\Model;

class SmsPrice extends Model
{
    protected $fillable = [
        'country',
        'currency',
        'actual_price',
        'margin',
        'channel',
    ];

    protected $casts = [
        'actual_price' => 'int',
        'margin' => 'int',
        'price' => 'int',
        'channel' => SmsChannel::class,
    ];
}
