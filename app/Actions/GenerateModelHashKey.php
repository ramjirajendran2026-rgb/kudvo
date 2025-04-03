<?php

namespace App\Actions;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenerateModelHashKey
{
    public function execute(Model $model, int $minHashLength = 6): string
    {
        $key = $model->getKey() ?: (DB::transaction(fn (): int => $model->newQuery()->lockForUpdate()->max($model->getKeyName())) + 1);

        $hashIds = new Hashids(salt: $model::class, minHashLength: $minHashLength);

        return $hashIds->encode($key);
    }
}
