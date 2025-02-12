@php
    use App\Settings\GoogleTagManagerSettings;
    use App\Settings\ServiceConfig;
    use Filament\FontProviders\LocalFontProvider;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    $googleTagManager = app(GoogleTagManagerSettings::class);
    $serviceConfig = app(ServiceConfig::class);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @if (app()->isProduction())
            {!! $googleTagManager->getHeadScript() !!}
        @endif

        {!! seo($seoData ?? null) !!}

        <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="{{ asset('apple-touch-icon.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="{{ asset('favicon-32x32.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="{{ asset('favicon-16x16.png') }}"
        />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')

        @stack('styles')
    </head>

    <body
        class="flex min-h-screen flex-col bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white"
    >
        {!! $googleTagManager->getBodyScript() !!}

        {{ $slot }}

        @livewire('notifications')

        @if ($serviceConfig->tawk_to->enabled)
            {!! $serviceConfig->tawk_to->script !!}
        @endif

        @filamentScripts
        @vite('resources/js/app.js')

        @stack('scripts')
    </body>
</html>
