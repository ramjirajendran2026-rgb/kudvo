<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class CreateStripePromotionCode extends CreateRecord
{
    protected static string $resource = StripePromotionCodeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        $data = $record->attributesToArray();
        $data['coupon'] = $record->coupon_id;
        $data['customer'] = $record->customer_id;
        $data['restrictions'] = array_filter($record->restrictions->toArray());

        unset($data['coupon_id'], $data['customer_id']);

        $promotionCode = Cashier::stripe()->promotionCodes->create(array_filter($data));

        $record->fill($promotionCode->toArray());
        $record->save();

        return $record;
    }
}
