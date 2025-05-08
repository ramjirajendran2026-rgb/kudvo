<?php

namespace App\Actions;

use App\Models\SmsPrice;
use App\Settings\SmsSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GetSmsPrices
{
    public function execute(string $currency, array $phones)
    {
        $minPrice = money(1, $currency);

        $countryPhones = collect($phones)
            ->filter()
            ->map(fn (string $phone) => [
                'phone' => $phone,
                'country' => phone($phone)->getCountry(),
            ])
            ->whereNotNull('country')
            ->groupBy('country')
            ->map(callback: fn (Collection $group, string $country) => [
                'country' => $country,
                'phones' => $group->count(),
            ])
            ->values();

        $smsSettings = app(SmsSettings::class);
        $countryChannels = collect($smsSettings->country_channel);
        $defaultChannel = $smsSettings->default_channel;

        $countryPrices = SmsPrice::query()->whereIn('country', $countryPhones->pluck('country')->unique())->get()
            ->groupBy('country')
            ->map(
                callback: fn (Collection $group, string $country) => $group
                    ->filter(fn (SmsPrice $price) => $price->channel->value === ($countryChannels->firstWhere('country', $country)['channel'] ?? $defaultChannel))
                    ->first(),
            )
            ->filter()
            ->map(
                callback: function (SmsPrice $smsPrice) use ($minPrice) {
                    $price = money($smsPrice->price, $smsPrice->currency)->mutable();

                    if (! $price->isSameCurrency($minPrice)) {
                        $rate = $this->getCurrencyRates($price->getCurrency()->getCurrency())[$minPrice->getCurrency()->getCurrency()];
                        $price->convert($minPrice->getCurrency(), $rate);

                        if ($price->lessThan($minPrice)) {
                            $price = $minPrice;
                        }
                    }

                    return (int) $price->getAmount();
                }
            );

        return $countryPhones
            ->map(callback: fn (array $countryPhone) => [
                'country' => $countryPhone['country'],
                'phones' => $countryPhone['phones'],
                'unit_price' => $countryPrices->get($countryPhone['country']),
            ])
            ->map(callback: fn (array $countryPhone) => [
                ...$countryPhone,
                'price' => $countryPhone['unit_price'] ? $countryPhone['unit_price'] * $countryPhone['phones'] : null,
            ]);
    }

    protected function getCurrencyRates(string $currency)
    {
        $currency = strtolower($currency);

        $rates = Cache::remember(
            'currency_rates.' . $currency . '.' . now()->format('Ymd'),
            60 * 60 * 24,
            fn () => Http::withoutVerifying()
                ->get(url: 'https://latest.currency-api.pages.dev/v1/currencies/' . $currency . '.json')
                ->json($currency),
        );

        return array_change_key_case($rates, CASE_UPPER);
    }
}
