<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource;
use App\Models\StripePromotionCode;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Laravel\Cashier\Cashier;
use Stripe\Customer;

class ListStripePromotionCodes extends ListRecords
{
    protected static string $resource = StripePromotionCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('syncAll')
                ->requiresConfirmation()
                ->action(function () {
                    $promotionCodes = Cashier::stripe()->promotionCodes->all();
                    $stripeIds = [];

                    foreach ($promotionCodes as $code) {
                        $stripeIds[] = $code->id;

                        $data = $code->toArray();
                        $data['coupon_id'] = $code->coupon->id;
                        $data['customer_id'] = $code->customer instanceof Customer ? $code->customer->id : $code->customer;

                        unset($data['coupon'], $data['customer']);

                        StripePromotionCode::createOrFirst(
                            ['id' => $code->id],
                            $data,
                        );

                        if (! $code->wasRecentlyCreated) {
                            $code->save();
                        }
                    }

                    StripePromotionCode::whereNotIn('id', $stripeIds)->delete();
                }),
        ];
    }
}
