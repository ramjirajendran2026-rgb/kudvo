<?php

namespace App\Models;

use App\Data\Election\ElectorFeeBreakupData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ElectionPrice extends Model
{
    protected $fillable = [
        'currency',
        'base_fee',
        'elector_fee_breakup',
        'enabled',
    ];

    protected $casts = [
        'base_fee' => 'int',
        'elector_fee_breakup' => ElectorFeeBreakupData::class,
        'enabled' => 'bool',
    ];

    protected function electorFee(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes): int => array_sum(array: $this->elector_fee_breakup->toArray() ?: []),
        );
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeCurrency(Builder $query, string $code): Builder
    {
        return $query->where('currency', $code);
    }
}
