<?php

namespace App\Models;

use App\Data\Stripe\PromotionCodeRestrictionsData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripePromotionCode extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'coupon_id',
        'metadata',
        'active',
        'created',
        'customer_id',
        'expires_at',
        'livemode',
        'max_redemptions',
        'restrictions',
        'times_redeemed',
    ];

    protected $casts = [
        'metadata' => 'array',
        'active' => 'bool',
        'created' => 'immutable_datetime',
        'expires_at' => 'immutable_datetime',
        'livemode' => 'bool',
        'max_redemptions' => 'int',
        'restrictions' => PromotionCodeRestrictionsData::class,
        'times_redeemed' => 'int',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(StripeCoupon::class, 'coupon_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'stripe_id');
    }
}
