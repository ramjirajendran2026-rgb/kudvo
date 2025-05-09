<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class EditStripeCoupon extends EditRecord
{
    protected static string $resource = StripeCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);

        Cashier::stripe()->coupons->update($record->getKey(), $record->only(['name', 'metadata']));

        $record->save();

        return $record;
    }
}
