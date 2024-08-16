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
            get: fn ($value, array $attributes) => blank($value) ? $this->generateShortCode() : $value,
            set: fn ($value) => $value,
        );
    }

    public function generateShortCode(bool $force = false): string
    {
        if (! $force && filled($shortCode = ($this->getAttributes()['short_code'] ?? null))) {
            return $shortCode;
        }

        do {
            try {
                $this->forceFill(attributes: ['short_code' => Str::random(length: 6)]);
                $this->save();
            } catch (UniqueConstraintViolationException) {
                $this->forceFill(attributes: ['short_code' => null]);
            }
        } while (blank($this->getAttributes()['short_code']));

        return $this->getAttributes()['short_code'];
    }
}
