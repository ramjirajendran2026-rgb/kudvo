<?php

namespace App\Data;

use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Exceptions\CannotCastData;

class EncryptedDataCollection extends DataCollection
{
    public static function castUsing(array $arguments)
    {
        if (count($arguments) < 1) {
            throw CannotCastData::dataCollectionTypeRequired();
        }

        return new EncryptedDataCollectionEloquentCast($arguments[0], DataCollection::class, array_slice($arguments, 1));
    }
}
