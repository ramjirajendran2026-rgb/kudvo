<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource\Pages;

use App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource;
use App\Models\StripeCoupon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Laravel\Cashier\Cashier;

class ListStripeCoupons extends ListRecords
{
    protected static string $resource = StripeCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('syncAll')
                ->requiresConfirmation()
                ->action(function () {
                    $cashierCoupons = Cashier::stripe()->coupons->all();
                    $stripeIds = [];

                    foreach ($cashierCoupons as $coupon) {
                        $stripeIds[] = $coupon->id;

                        $coupon = StripeCoupon::createOrFirst(
                            ['id' => $coupon->id],
                            [
                                'name' => $coupon->name,
                                'percent_off' => $coupon->percent_off,
                                'duration' => $coupon->duration,
                                'amount_off' => $coupon->amount_off,
                                'currency' => $coupon->currency,
                                'max_redemptions' => $coupon->max_redemptions,
                                'redeem_by' => $coupon->redeem_by,
                                'times_redeemed' => $coupon->times_redeemed,
                                'metadata' => $coupon->metadata?->toArray(),
                                'valid' => $coupon->valid,
                                'created' => $coupon->created,
                                'livemode' => $coupon->livemode,
                            ],
                        );

                        if (! $coupon->wasRecentlyCreated) {
                            $coupon->save();
                        }
                    }

                    if (filled($stripeIds)) {
                        //                        StripeCoupon::whereNotIn('id', $stripeIds)->delete();
                    }
                }),
        ];
    }
}
