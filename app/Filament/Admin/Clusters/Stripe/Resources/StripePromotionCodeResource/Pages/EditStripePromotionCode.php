<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class EditStripePromotionCode extends EditRecord
{
    protected static string $resource = StripePromotionCodeResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);

        Cashier::stripe()->promotionCodes->update($record->getKey(), $record->only(['active', 'metadata']));

        $record->save();

        return $record;
    }
}
