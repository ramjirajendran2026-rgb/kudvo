@php
    use Filament\FontProviders\LocalFontProvider;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? config('app.name') }}</title>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite('resources/css/pdf.css')

        {{ app(LocalFontProvider::class)->getHtml(family: 'Poppins') }}

        @stack('styles')
    </head>

    <body
        class="min-h-screen bg-gray-50 font-normal text-gray-950 antialiased"
    >
        {{ $slot }}

        @stack('scripts')
    </body>
</html>
