<x-layouts.base :seo-data="$seoData ?? null">
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
                    title="{{ config('app.name') }} home page"
                    class="flex cursor-pointer items-center gap-2"
                >
                    <img
                        src="{{ asset('img/nav-logo.webp') }}"
                        alt="{{ config('app.name') }} logo"
                        title="{{ config('app.name') }} logo"
                        class="size-12"
                    />
                    <span
                        class="text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white"
                    >
                        {{ config('app.name') }}
                    </span>
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
                <li class="fi-topbar-item">
                    <x-filament::dropdown placement="bottom-start" teleport>
                        <x-slot name="trigger">
                            <ul>
                                <x-filament-panels::topbar.item>
                                    {{ __('app.nav.products.label') }}
                                </x-filament-panels::topbar.item>
                            </ul>
                        </x-slot>

                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item
                                :href="route('products.election.home')"
                                icon="heroicon-o-computer-desktop"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.election.label') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                :href="route('products.phygital.home')"
                                icon="heroicon-o-archive-box"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.phygital.label') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                :disabled="true"
                                icon="heroicon-o-scale"
                                tag="a"
                                href="#"
                                badge="soon"
                            >
                                {{ __('app.nav.products.items.resolution_voting.label') }}
                            </x-filament::dropdown.list.item>

                            <x-filament::dropdown.list.item
                                :href="route('products.survey.home')"
                                icon="heroicon-o-document-chart-bar"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.survey.label') }}
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </li>

                <x-filament-panels::topbar.item
                    :url="route('home') . '#clientele'"
                >
                    {{ __('app.nav.clientele.label') }}
                </x-filament-panels::topbar.item>

                <x-filament-panels::topbar.item :url="route('wiki.index')">
                    {{ __('app.nav.wiki.label') }}
                </x-filament-panels::topbar.item>

                <x-filament-panels::topbar.item
                    :url="route('home') . '#contact'"
                >
                    {{ __('app.nav.contact.label') }}
                </x-filament-panels::topbar.item>
            </ul>

            <div class="ms-auto hidden gap-x-4 lg:flex lg:items-center">
                <x-locale-switch />

                <x-filament::button
                    :href="route('vote-now')"
                    color="success"
                    tag="a"
                >
                    {{ __('app.nav.vote_now.label') }}
                </x-filament::button>

                @auth
                    <x-filament::button
                        :href="filament()->getPanel('user')->getUrl()"
                        rel="nofollow"
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
                                :href="route('products.phygital.home')"
                                icon="heroicon-o-archive-box"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.phygital.label') }}
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

                            <x-filament::dropdown.list.item
                                :href="route('products.survey.home')"
                                icon="heroicon-o-document-chart-bar"
                                tag="a"
                            >
                                {{ __('app.nav.products.items.survey.label') }}
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>

                    <x-filament-panels::topbar.item
                        :url="route('home') . '#clientele'"
                    >
                        {{ __('app.nav.clientele.label') }}
                    </x-filament-panels::topbar.item>

                    <x-filament-panels::topbar.item :url="route('wiki.index')">
                        {{ __('app.nav.wiki.label') }}
                    </x-filament-panels::topbar.item>

                    <x-filament-panels::topbar.item
                        :url="route('home') . '#contact'"
                    >
                        {{ __('app.nav.contact.label') }}
                    </x-filament-panels::topbar.item>
                </ul>

                <div class="flex gap-x-4">
                    <x-locale-switch />

                    <x-filament::button
                        :href="route('vote-now')"
                        color="success"
                        tag="a"
                        class="flex-1"
                    >
                        {{ __('app.nav.vote_now.label') }}
                    </x-filament::button>

                    @auth
                        <x-filament::button
                            :href="filament()->getPanel('user')->getUrl()"
                            rel="nofollow"
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
        <div class="bg-primary-800 text-white md:flex-row">
            <div
                class="container flex flex-col items-center justify-center px-4 py-4 sm:flex-row sm:gap-4 lg:px-8"
            >
                <span>
                    &copy; {{ date('Y ') . ' - ' . config('app.name') }}
                </span>
                <span class="hidden sm:inline">•</span>
                <a
                    href="{{ route('privacy-policy') }}"
                    class="hover:underline"
                >
                    {{ __('app.nav.privacy_policy.label') }}
                </a>
            </div>
        </div>
    </footer>
</x-layouts.base>
