<?php

namespace App\Models;

use App\Enums\StripeCouponDuration;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class StripeCoupon extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'amount_off',
        'currency',
        'duration',
        'metadata',
        'name',
        'percent_off',
        'created',
        'currency_options',
        'livemode',
        'max_redemptions',
        'redeem_by',
        'times_redeemed',
        'valid',
    ];

    protected $casts = [
        'amount_off' => 'int',
        'duration' => StripeCouponDuration::class,
        'metadata' => 'array',
        'percent_off' => 'double',
        'created' => 'immutable_datetime',
        'currency_options' => 'array',
        'livemode' => 'bool',
        'max_redemptions' => 'int',
        'redeem_by' => 'immutable_datetime',
        'times_redeemed' => 'int',
        'valid' => 'bool',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::deleted(function ($coupon) {
            Cashier::stripe()->coupons->delete($coupon->id);
        });
    }
}
