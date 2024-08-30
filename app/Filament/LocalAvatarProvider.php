<?php

namespace App\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Spatie\Color\Rgb;

class LocalAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->squish()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return Cache::rememberForever(
            'local-avatar-' . Str::kebab($name),
            fn () => 'data:image/svg+xml;base64,' .
                base64_encode(
                    app(InitialAvatar::class)
                        ->background(Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][950] . ')')->toHex())
                        ->color('#FFFFFF')
                        ->name($name)
                        ->generateSvg()
                        ->toXMLString()
                )
        );
    }
}
