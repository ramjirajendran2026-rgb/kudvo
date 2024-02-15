<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

trait HasShortCode
{
    protected function shortCode(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => blank($value) ? $this->generateShortCode() : $value,
            set: fn($value) => $value,
        );
    }

    public function generateShortCode(): string
    {
        do {
            try {
                $this->short_code = Str::random(length: 6);
                $this->save();
            } catch (UniqueConstraintViolationException) {
                $this->short_code = null;
            }
        } while (blank($this->short_code));

        return $this->short_code;
    }
}
