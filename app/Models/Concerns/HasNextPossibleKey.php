<?php

namespace App\Models\Concerns;

trait HasNextPossibleKey
{
    public function getNextPossibleKey(): int
    {
        return (static::max($this->getKeyName()) ?: 0) + 1;
    }
}
