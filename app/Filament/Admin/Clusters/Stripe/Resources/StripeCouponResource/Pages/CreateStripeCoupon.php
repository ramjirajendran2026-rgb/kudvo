<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class CreateStripeCoupon extends CreateRecord
{
    protected static string $resource = StripeCouponResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);
        $coupon = Cashier::stripe()->coupons->create(array_filter($record->attributesToArray()));
        $record->fill($coupon->toArray());
        $record->save();

        return $record;
    }
}
