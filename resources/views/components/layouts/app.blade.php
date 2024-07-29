@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />

        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        {!! seo($seoData ?? null) !!}

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet"
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
        class="min-h-[110vh] bg-gray-50 font-[Poppins] font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white"
    >
        <header
            x-data="{ isSticky: false }"
            x-bind:class="{
                'bg-white shadow-lg dark:bg-gray-900 dark:ring-white/10 ring-1 ring-gray-950/5 mb-0':
                    isSticky,
            }"
            x-on:scroll.window="isSticky = window.scrollY > 16"
            class="sticky top-0 z-20 mb-4 mt-4 overflow-x-clip transition-all md:px-6 lg:px-8"
        >
            <nav
                x-data="{
                    isOpen: false,

                    collapsedGroups: [],

                    groupIsCollapsed: function (group) {
                        return this.collapsedGroups.includes(group)
                    },

                    collapseGroup: function (group) {
                        if (this.collapsedGroups.includes(group)) {
                            return
                        }

                        this.collapsedGroups = this.collapsedGroups.concat(group)
                    },

                    toggleCollapsedGroup: function (group) {
                        this.collapsedGroups = this.collapsedGroups.includes(group)
                            ? this.collapsedGroups.filter(
                                  (collapsedGroup) => collapsedGroup !== group,
                              )
                            : this.collapsedGroups.concat(group)
                    },

                    close: function () {
                        this.isOpen = false
                    },

                    open: function () {
                        this.isOpen = true
                    },
                }"
                class="container flex h-16 gap-x-4 lg:items-center lg:justify-between"
            >
                <div class="flex w-full items-center justify-between lg:w-auto">
                    <a
                        href="{{ route('home') }}"
                        class="flex cursor-pointer items-center gap-2"
                    >
                        <img
                            src="{{ asset('img/nav-logo.png') }}"
                            alt="Logo"
                            class="h-12 w-12"
                        />
                        <div
                            class="text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white"
                        >
                            {{ config('app.name') }}
                        </div>
                    </a>

                    <x-filament::icon-button
                        color="gray"
                        class="lg:hidden"
                        icon="heroicon-o-bars-3"
                        icon-size="lg"
                        :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                        x-cloak
                        x-data="{}"
                        x-on:click="open()"
                        x-show="! isOpen"
                    />

                    <x-filament::icon-button
                        color="gray"
                        icon="heroicon-o-x-mark"
                        icon-size="lg"
                        :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                        x-cloak
                        x-data="{}"
                        x-on:click="close()"
                        x-show="isOpen"
                        class="lg:hidden"
                    />
                </div>

                <ul
                    class="hidden flex-1 items-center gap-x-4 lg:flex lg:justify-center"
                >
                    <x-filament-panels::topbar.item :url="route('home')">
                        {{ __('app.nav.home.label') }}
                    </x-filament-panels::topbar.item>

                    <x-filament::dropdown placement="bottom-start" teleport>
                        <x-slot name="trigger">
                            <x-filament-panels::topbar.item>
                                {{ __('app.nav.products.label') }}
                            </x-filament-panels::topbar.item>
                        </x-slot>

                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item
                                :href="route('products.election.home')"
                                icon="heroicon-o-archive-box"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.election.label') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                :disabled="true"
                                icon="heroicon-o-scale"
                                tag="a"
                                href="#"
                                badge="coming"
                            >
                                {{ __('app.nav.products.items.resolution_voting.label') }}
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>

                    <x-filament-panels::topbar.item
                        :url="route('home') . '#clientele'"
                    >
                        {{ __('app.nav.clientele.label') }}
                    </x-filament-panels::topbar.item>

                    {{--
                        <x-filament-panels::topbar.item url="#">
                        {{ __('app.nav.blog.label') }}
                        </x-filament-panels::topbar.item>
                    --}}

                    <x-filament-panels::topbar.item
                        :url="route('home') . '#contact'"
                    >
                        {{ __('app.nav.contact.label') }}
                    </x-filament-panels::topbar.item>
                </ul>

                <div class="ms-auto hidden gap-x-4 lg:flex lg:items-center">
                    <x-locale-switch />

                    @auth
                        <x-filament::button
                            :href="route('filament.user.auth.login')"
                            tag="a"
                        >
                            {{ __('app.nav.dashboard.label') }}
                        </x-filament::button>
                    @else
                        <x-filament::button
                            color="gray"
                            :href="route('filament.user.auth.login')"
                            tag="a"
                        >
                            {{ __('app.nav.sign_in.label') }}
                        </x-filament::button>

                        <x-filament::button
                            :href="route('filament.user.auth.register')"
                            tag="a"
                        >
                            {{ __('app.nav.sign_up.label') }}
                        </x-filament::button>
                    @endauth
                </div>

                <div
                    x-cloak
                    x-show="isOpen"
                    @click.away="isOpen = false"
                    class="absolute inset-x-2 top-20 z-50 origin-top-right transform space-y-6 rounded-xl bg-white px-4 py-6 shadow-lg transition lg:hidden"
                >
                    <ul
                        class="flex w-full flex-col items-start space-y-4 lg:flex-row"
                    >
                        <x-filament-panels::topbar.item :url="route('home')">
                            {{ __('app.nav.home.label') }}
                        </x-filament-panels::topbar.item>

                        <x-filament::dropdown placement="bottom-start" teleport>
                            <x-slot name="trigger">
                                <x-filament-panels::topbar.item>
                                    {{ __('app.nav.products.label') }}
                                </x-filament-panels::topbar.item>
                            </x-slot>

                            <x-filament::dropdown.list>
                                <x-filament::dropdown.list.item
                                    :href="route('products.election.home')"
                                    icon="heroicon-o-archive-box"
                                    tag="a"
                                >
                                    {{ __('app.nav.products.items.election.label') }}
                                </x-filament::dropdown.list.item>

                                <x-filament::dropdown.list.item
                                    :disabled="true"
                                    icon="heroicon-o-scale"
                                    tag="a"
                                    href="#"
                                    tooltip="Coming soon"
                                    badge="coming"
                                >
                                    {{ __('app.nav.products.items.resolution_voting.label') }}
                                </x-filament::dropdown.list.item>
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>

                        <x-filament-panels::topbar.item
                            :url="route('home') . '#clientele'"
                        >
                            {{ __('app.nav.clientele.label') }}
                        </x-filament-panels::topbar.item>

                        {{--
                            <x-filament-panels::topbar.item url="#">
                            {{ __('app.nav.blog.label') }}
                            </x-filament-panels::topbar.item>
                        --}}

                        <x-filament-panels::topbar.item
                            :url="route('home') . '#contact'"
                        >
                            {{ __('app.nav.contact.label') }}
                        </x-filament-panels::topbar.item>
                    </ul>

                    <div class="flex gap-x-4">
                        <x-locale-switch />

                        @auth
                            <x-filament::button
                                :href="route('filament.user.auth.login')"
                                tag="a"
                                class="flex-1"
                            >
                                {{ __('app.nav.dashboard.label') }}
                            </x-filament::button>
                        @else
                            <x-filament::button
                                color="gray"
                                :href="route('filament.user.auth.login')"
                                tag="a"
                                class="flex-1"
                            >
                                {{ __('app.nav.sign_in.label') }}
                            </x-filament::button>

                            <x-filament::button
                                :href="route('filament.user.auth.register')"
                                tag="a"
                                class="flex-1"
                            >
                                {{ __('app.nav.sign_up.label') }}
                            </x-filament::button>
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        {{ $slot }}

        <footer class="border-t bg-white">
            <div
                class="container grid grid-cols-2 gap-4 px-4 py-8 md:grid-cols-4 md:gap-8 md:px-6 lg:px-8"
            >
                <div class="space-y-4">
                    <p class="text-xl font-semibold">
                        {{ __('app.nav.products.label') }}
                    </p>
                    <ul class="list-none space-y-2">
                        <li>
                            <a href="{{ route('products.election.home') }}">
                                {{ __('app.nav.products.items.election.label') }}
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                {{ __('app.nav.products.items.resolution_voting.label') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="text-xl font-semibold">
                        {{ __('app.footer.quick_links.label') }}
                    </p>
                    <ul class="list-none space-y-2">
                        <li>
                            <a href="{{ route('home') }}">
                                {{ __('app.nav.home.label') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}#contact">
                                {{ __('app.nav.contact.label') }}
                            </a>
                        </li>
                        {{--
                            <li>
                            <a href="#">{{ __('app.nav.blog.label') }}</a>
                            </li>
                        --}}
                        <li>
                            <a href="{{ route('privacy-policy') }}">
                                {{ __('app.nav.privacy_policy.label') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div
                    class="col-span-full space-y-4 text-center md:col-span-2 md:text-start"
                >
                    <p>{{ __('app.footer.description') }}</p>
                    <div
                        class="flex items-center justify-center gap-4 md:justify-start"
                    >
                        <x-filament::icon
                            icon="heroicon-o-phone"
                            class="h-6 w-6"
                        />
                        <div class="text-lg">
                            <span>{{ __('app.contact.phone.label') }}</span>
                            <a
                                href="tel:{{ __('app.contact.phone.number') }}"
                                class="cursor-pointer text-nowrap"
                            >
                                {{ __('app.contact.phone.number') }}
                            </a>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-center gap-4 md:justify-start"
                    >
                        <x-filament::icon
                            icon="heroicon-o-envelope"
                            class="h-6 w-6"
                        />
                        <a
                            href="mailto://{{ __('app.contact.email.address') }}"
                            class="cursor-pointer text-lg"
                        >
                            {{ __('app.contact.email.address') }}
                        </a>
                    </div>
                    <div class="flex justify-center gap-3 md:justify-start">
                        <a href="#" title="Facebook link">
                            <svg
                                class="hover:fill-primary"
                                fill="#000000"
                                width="40px"
                                height="40px"
                                viewBox="0 0 32 32"
                                version="1.1"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g
                                    id="SVGRepo_tracerCarrier"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></g>
                                <g id="SVGRepo_iconCarrier">
                                    <title>facebook</title>
                                    <path
                                        d="M30.996 16.091c-0.001-8.281-6.714-14.994-14.996-14.994s-14.996 6.714-14.996 14.996c0 7.455 5.44 13.639 12.566 14.8l0.086 0.012v-10.478h-3.808v-4.336h3.808v-3.302c-0.019-0.167-0.029-0.361-0.029-0.557 0-2.923 2.37-5.293 5.293-5.293 0.141 0 0.281 0.006 0.42 0.016l-0.018-0.001c1.199 0.017 2.359 0.123 3.491 0.312l-0.134-0.019v3.69h-1.892c-0.086-0.012-0.185-0.019-0.285-0.019-1.197 0-2.168 0.97-2.168 2.168 0 0.068 0.003 0.135 0.009 0.202l-0.001-0.009v2.812h4.159l-0.665 4.336h-3.494v10.478c7.213-1.174 12.653-7.359 12.654-14.814v-0z"
                                    ></path>
                                </g>
                            </svg>
                        </a>

                        <a href="#" title="Linkedin link">
                            <svg
                                class="hover:fill-primary"
                                fill="#000000"
                                width="40px"
                                height="40px"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g
                                    id="SVGRepo_tracerCarrier"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                ></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path
                                        d="M10 .4C4.698.4.4 4.698.4 10s4.298 9.6 9.6 9.6 9.6-4.298 9.6-9.6S15.302.4 10 .4zM7.65 13.979H5.706V7.723H7.65v6.256zm-.984-7.024c-.614 0-1.011-.435-1.011-.973 0-.549.409-.971 1.036-.971s1.011.422 1.023.971c0 .538-.396.973-1.048.973zm8.084 7.024h-1.944v-3.467c0-.807-.282-1.355-.985-1.355-.537 0-.856.371-.997.728-.052.127-.065.307-.065.486v3.607H8.814v-4.26c0-.781-.025-1.434-.051-1.996h1.689l.089.869h.039c.256-.408.883-1.01 1.932-1.01 1.279 0 2.238.857 2.238 2.699v3.699z"
                                    ></path>
                                </g>
                            </svg>
                        </a>

                        <a href="#" title="Twitter link">
                            <svg
                                class="hover:fill-primary"
                                xmlns="http://www.w3.org/2000/svg"
                                x="0px"
                                y="0px"
                                width="40"
                                height="40"
                                viewBox="0 0 50 50"
                            >
                                <path
                                    d="M 11 4 C 7.134 4 4 7.134 4 11 L 4 39 C 4 42.866 7.134 46 11 46 L 39 46 C 42.866 46 46 42.866 46 39 L 46 11 C 46 7.134 42.866 4 39 4 L 11 4 z M 13.085938 13 L 21.023438 13 L 26.660156 21.009766 L 33.5 13 L 36 13 L 27.789062 22.613281 L 37.914062 37 L 29.978516 37 L 23.4375 27.707031 L 15.5 37 L 13 37 L 22.308594 26.103516 L 13.085938 13 z M 16.914062 15 L 31.021484 35 L 34.085938 35 L 19.978516 15 L 16.914062 15 z"
                                ></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-primary-800 text-white md:flex-row">
                <div
                    class="container flex flex-col items-center justify-center px-4 py-4 md:flex-row lg:px-8"
                >
                    <span>
                        &copy; {{ date('Y ') . ' - ' . config('app.name') }}
                    </span>
                </div>
            </div>
        </footer>

        @livewire('notifications')

        @filamentScripts
        @vite('resources/js/app.js')

        @stack('scripts')
    </body>
</html>
