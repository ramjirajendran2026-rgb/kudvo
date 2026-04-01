@extends('layouts.app')

<style>
    .form-control {
        width: 100% !important;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        color: #333;
        transition: all 0.3s ease-in-out;
    }
</style>

@section('content')

<main wire:snapshot="{&quot;data&quot;:[],&quot;memo&quot;:{&quot;id&quot;:&quot;we8fHRPm2iVEfyNjin3W&quot;,&quot;name&quot;:&quot;pages.home&quot;,&quot;path&quot;:&quot;\/&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;release&quot;:&quot;a-a-a&quot;,&quot;children&quot;:{&quot;lw-4052353981-0&quot;:[&quot;div&quot;,&quot;asUPr8aspihVChTG2BSZ&quot;]},&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;errors&quot;:[],&quot;locale&quot;:&quot;en&quot;},&quot;checksum&quot;:&quot;33177e3e2408407b7c40f91a1f9a8d831c3726d29d81ac247474c9977a342d24&quot;}" wire:effects="[]" wire:id="we8fHRPm2iVEfyNjin3W" class="page-home min-h-screen w-full overflow-hidden bg-gradient-to-b from-gray-50 to-blue-50 font-sans">
    <section id="hero" class="container relative overflow-hidden !px-0 md:!px-6 xl:!px-0" x-data="{
            started: false,
            activeSlide: 0,
            slides: 7,
            intervalId: null,
            slideItems: 

            startSlider() {
                this.intervalId = setInterval(() =&gt; {
                    this.activeSlide = (this.activeSlide + 1) % this.slides

                    if (! this.started) {
                        this.started = true
                    }
                }, 5000)
            },

            prevSlide() {
                this.activateSlide(this.activeSlide - 1 + this.slides)
            },

            nextSlide() {
                this.activateSlide(this.activeSlide + 1)
            },

            activateSlide(slide) {
                if (this.intervalId !== null) {
                    clearInterval(this.intervalId)
                }

                this.activeSlide = slide % this.slides
                this.intervalId = setInterval(() =&gt; {
                    this.activeSlide = (this.activeSlide + 1) % this.slides
                }, 5000)
            },
        }" x-init="startSlider()">
        <button aria-label="Previous slide" @click="prevSlide()" class="absolute left-5 top-1/2 z-10 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 opacity-10 hover:opacity-100 focus:outline-none">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button aria-label="Next slide" @click="nextSlide()" class="absolute right-5 top-1/2 z-10 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 opacity-10 hover:opacity-100 focus:outline-none">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!--[if BLOCK]><![endif]-->            <div x-data="{ currentSlide: 0 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/online-voting-system.webp" alt=" A computer and a voting machine are displayed against a backdrop of various icons representing technology and democracy." title="eVote Made Simple. Strengthening Democracy with Kudvo" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            eVote Made Simple. Strengthening Democracy with Kudvo
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Increase voter turnout, enhance security, and streamline your election process with our innovative platform.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            eVote Made Simple. Strengthening Democracy with Kudvo
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Increase voter turnout, enhance security, and streamline your election process with our innovative platform.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/online-voting-system.webp" alt=" A computer and a voting machine are displayed against a backdrop of various icons representing technology and democracy." title="eVote Made Simple. Strengthening Democracy with Kudvo" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 1 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/club.webp" alt="Collage of sports and leisure activities including golf, tennis, soccer, and a marina with boats, showcasing a vibrant club environment." title="Effortless Online Voting for Clubs: Secure and Reliable eVote Solutions" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Effortless Online Voting for Clubs: Secure and Reliable eVote Solutions
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Streamline your decision-making process with our trusted system.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Effortless Online Voting for Clubs: Secure and Reliable eVote Solutions
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Streamline your decision-making process with our trusted system.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/club.webp" alt="Collage of sports and leisure activities including golf, tennis, soccer, and a marina with boats, showcasing a vibrant club environment." title="Effortless Online Voting for Clubs: Secure and Reliable eVote Solutions" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 2 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/hoa-home-owner-asspciation-or-condominium-associations.webp" alt="Aerial view of a suburban neighborhood with well-maintained houses and greenery, representing a homeowner association community." title="Revolutionizing Homeowners Association Governance with Online Elections" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Revolutionizing Homeowners Association Governance with Online Elections
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Manage condominium community decisions</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Revolutionizing Homeowners Association Governance with Online Elections
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Manage condominium community decisions</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/hoa-home-owner-asspciation-or-condominium-associations.webp" alt="Aerial view of a suburban neighborhood with well-maintained houses and greenery, representing a homeowner association community." title="Revolutionizing Homeowners Association Governance with Online Elections" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 3 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/corparate-industry.webp" alt="Industrial cityscape with a large chemical plant in the center, surrounded by skyscrapers and green spaces." title="Simplify Corporate Decisions with Kudvo’s Secure Online Resolution Voting System" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Simplify Corporate Decisions with Kudvo’s Secure Online Resolution Voting System
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>offers a convenient solution for board meetings, corporate resolutions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Simplify Corporate Decisions with Kudvo’s Secure Online Resolution Voting System
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>offers a convenient solution for board meetings, corporate resolutions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/corparate-industry.webp" alt="Industrial cityscape with a large chemical plant in the center, surrounded by skyscrapers and green spaces." title="Simplify Corporate Decisions with Kudvo’s Secure Online Resolution Voting System" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 4 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/associations-and-unions.webp" alt="Diverse group of people connected by lines and circles, representing a network or collaboration." title="Boost Engagement in Associations &amp; Unions through Online Voting" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Boost Engagement in Associations &amp; Unions through Online Voting
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Foster stronger engagement among your association or union members.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Boost Engagement in Associations &amp; Unions through Online Voting
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Foster stronger engagement among your association or union members.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/associations-and-unions.webp" alt="Diverse group of people connected by lines and circles, representing a network or collaboration." title="Boost Engagement in Associations &amp; Unions through Online Voting" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 5 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center animated-image" src="http://127.0.0.1:8000/img/home/hero/school-or-university.webp" alt="Aerial view of a modern university campus with multiple buildings, landscaped grounds, and walking paths." title="Transform Educational Institutions with Trusted Online Election Tools" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16 animated-image" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Transform Educational Institutions with Trusted Online Election Tools
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Encourage student and staff participation in institutional decisions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Transform Educational Institutions with Trusted Online Election Tools
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Encourage student and staff participation in institutional decisions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64 animated-image" src="http://127.0.0.1:8000/img/home/hero/school-or-university.webp" alt="Aerial view of a modern university campus with multiple buildings, landscaped grounds, and walking paths." title="Transform Educational Institutions with Trusted Online Election Tools" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
                    <div x-data="{ currentSlide: 6 }" x-show="activeSlide === currentSlide" class="fade-in relative flex flex-col overflow-hidden md:aspect-[2.23/1] lg:flex-row lg:pb-0 lg:pt-0" tabindex="0" aria-live="polite" style="display: none;">
                <!-- Large screens: image as background with overlay card -->
                <div class="relative hidden h-56 w-full sm:h-72 md:block md:h-[420px] lg:h-[480px] xl:h-[540px]">
                    <img loading="lazy" class="absolute inset-0 h-full w-full rounded-2xl object-cover object-center" src="http://127.0.0.1:8000/img/home/hero/employer-associations.webp" alt="The image shows a diverse group of professionals smiling. They are dressed in different work clothes, including business suits and safety gear, indicating various professions." title="Empower Employee Associations with Seamless Online Voting Platforms" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                    <div class="fade-in absolute inset-0 mx-4 flex max-w-full flex-col justify-center px-4 py-6 shadow-lg glass neumorph sm:mx-10 sm:px-8 sm:py-8 md:mx-0 md:w-[66.6%] md:px-12 md:py-12 lg:w-1/2 lg:px-16" x-bind:class="{ 'animated-image': activeSlide === currentSlide }">
                        <h2 class="xs:text-2xl mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl md:text-3xl lg:text-4xl xl:text-5xl">
                            Empower Employee Associations with Seamless Online Voting Platforms
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg md:text-xl">
                            <li>Empower your workforce with a voice in organizational decisions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:gap-5">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base md:px-8 md:py-4 md:text-lg" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                </div>
                <!-- Small screens: image below content card -->
                <div class="flex w-full flex-col p-2 md:hidden">
                    <div class="fade-in px-3 py-5 shadow-lg glass neumorph sm:px-6 sm:py-6">
                        <h2 class="xs:text-2xl contrast:text-white mb-4 break-words text-xl font-extrabold leading-tight tracking-tight text-gray-900 drop-shadow-md sm:text-3xl">
                            Empower Employee Associations with Seamless Online Voting Platforms
                        </h2>
                        <ul class="xs:text-base contrast:text-gray-200 mb-4 text-sm text-gray-700 sm:text-lg">
                            <li>Empower your workforce with a voice in organizational decisions.</li>
                        </ul>
                        <div class="mt-2 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/app/register" class="xs:text-sm px-5 py-2 text-center text-xs btn-primary focus-outline sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Get Started
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]-->                                <a href="http://127.0.0.1:8000/products/online-voting" class="xs:text-sm bg-green-500 px-5 py-2 text-center text-xs btn-primary focus-outline hover:bg-green-600 sm:px-6 sm:py-3 sm:text-base" tabindex="0" role="button">
                                    Explore
                                </a>
                            <!--[if ENDBLOCK]><![endif]-->                        </div>
                    </div>
                    <img loading="lazy" class="mt-4 h-48 w-full rounded-2xl object-cover object-center sm:h-64" src="http://127.0.0.1:8000/img/home/hero/employer-associations.webp" alt="The image shows a diverse group of professionals smiling. They are dressed in different work clothes, including business suits and safety gear, indicating various professions." title="Empower Employee Associations with Seamless Online Voting Platforms" x-bind:class="{ 'animated-image': activeSlide === currentSlide &amp;&amp; started }" style="filter: brightness(0.97)">
                </div>
            </div>
        <!--[if ENDBLOCK]><![endif]-->
        <div class="flex justify-center md:mt-4 xl:mt-0">
            <template x-for="slide in slides" :key="slide">
                <button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only"></span>
                </button>
            </template><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 1 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 2 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 3 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 4 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 5 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-primary-600" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 6 selector</span>
                </button><button @click="activateSlide(slide - 1)" :class="{ 'bg-primary-600': activeSlide === slide - 1, 'bg-gray-200': activeSlide !== slide - 1 }" class="mx-1 h-3 w-6 rounded-full focus:outline-none bg-gray-200" x-transition:enter="transition-all duration-300 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition-all duration-200 ease-out" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0">
                    <span x-text="'Slider '+slide+' selector'" class="sr-only">Slider 7 selector</span>
                </button>
        </div>
    </section>

    <section id="intro" class="container pt-16">
        <h1 class="mx-auto max-w-screen-lg text-center text-xl font-bold text-gray-900 sm:text-2xl md:text-4xl">
            Secure and Efficient eVote Solutions for Your Online Election System        </h1>
    </section>

    <section id="features" class="container pt-16">
        <h2 class="text-center text-2xl font-semibold text-black md:text-4xl">
            Explore Our Features
        </h2>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 md:gap-8 lg:grid-cols-3">
            <!--[if BLOCK]><![endif]-->                <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/ballot-link-delivery.webp" alt="A person in a green shirt looks at a smartphone with a message: &quot;your Ballot link is https://kudvo.com/b/SRThs to vote for elections&quot; alongside SMS and email icons." title="Secure and Convenient Ballot Access" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Secure and Convenient Ballot Access
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Simplify the voting process with quick and efficient access to the ballot.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Offer unique links or a common access point for eligible voters.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Ensure convenience and reliability for voters.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
                            <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/multi-factor-authentication-code-delivery.webp" alt="Screenshot of an acknowledgment from Kudvo confirming Mr. Vikram T vote for iNodesys on Feb 21, 2024, at 05:02 PM (IST). The document serves as a ballot copy." title="Enhanced Security with Multi-Factor Authentication" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Enhanced Security with Multi-Factor Authentication
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Prioritize the integrity of the voting process with enhanced security measures.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Implement multi-factor authentication (MFA) codes for voter verification.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Protect against fake or unauthorized votes, safeguarding the validity of election results.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
                            <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/ballot-acknowledgement.webp" alt="An image showing a verification message with the code &quot;715363&quot; sent by &quot;iNodesys&quot; for OTP verification." title="Transparent Ballot Acknowledgement" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Transparent Ballot Acknowledgement
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Provide voters with confirmation of their ballots to ensure transparency.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Verify the authenticity of votes and enhance voter satisfaction with the process.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Offer additional support and resources to voters, enhancing their overall experience.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
                            <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/do-not-track-vote.webp" alt="A man in a suit and face mask stands at a podium with a laptop, next to graphics emphasizing voter privacy and security." title="Advanced Security Preferences" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Advanced Security Preferences
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Customize security preferences to track and prevent duplicate devices.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Implement advanced tracking and prevention features to protect against voting fraud.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Ensure the integrity of elections with robust security measures in place.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
                            <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/elector-update-after-publish.webp" alt="A woman in business attire smiles while holding a laptop. A shaded panel behind her displays options for allowing elector updates with a 'Publish' button below." title="Comprehensive Election Management" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Comprehensive Election Management
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Keep elector details updated even after the election has been published.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Ensure the accuracy of voter information and election results.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Provide a seamless and reliable voting experience for all participants.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
                            <div data-aos="fade-up" class="overflow-hidden rounded-2xl border border-primary-100 bg-white/80 font-sans shadow-md backdrop-blur-md transition-all glass neumorph hover:shadow-lg aos-init">
    
    <div class="flex min-h-[96px] items-center gap-4 bg-primary-100 p-4 sm:p-6">
        <div class="shrink-0 rounded-xl bg-white p-2 shadow-sm">
            <img loading="lazy" src="http://127.0.0.1:8000/img/home/features/segmented-voting-system.webp" alt="Illustration of a tree with blue circular nodes connected by lines. Some nodes contain smaller orange and white circles. The background is a light teal color." title="Segmented Ballot for Enhanced Efficiency" class="h-12 w-12 object-contain">
        </div>
        <h3 class="text-lg font-extrabold leading-snug text-gray-800 sm:text-xl">
            Segmented Ballot for Enhanced Efficiency
        </h3>
    </div>

    
    <ul class="space-y-3 p-5 text-sm text-gray-700 sm:p-6 sm:text-base">
        <!--[if BLOCK]><![endif]-->            <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Divide the ballot based on elector details to make voting more efficient.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Offer targeted voting experiences and better engage voters with personalized ballots.</span>
            </li>
                    <li class="flex items-start gap-2">
                <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
  <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"></path>
</svg>                <span class="leading-snug">Enhance the overall voting experience and increase voter participation.</span>
            </li>
        <!--[if ENDBLOCK]><![endif]-->    </ul>
</div>
            <!--[if ENDBLOCK]><![endif]-->        </div>
    </section>

    <section id="products" class="container pt-16">
        <div class="space-y-6 rounded-3xl bg-primary-700/30 p-6 md:space-y-8 md:p-8">
            <h4 class="text-center text-2xl font-semibold text-primary-950 md:text-4xl">
                Products
            </h4>
            <div class="grid gap-4 sm:grid-cols-2 md:gap-8 lg:grid-cols-3">
                <!--[if BLOCK]><![endif]-->                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #008F78">
            Online Election
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Host a variety of elections with ease using Kudvo's Online Election solution. Simplify board elections, committee votes, or organizational polls with adaptable voting methods.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<a href="http://127.0.0.1:8000/products/online-voting" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Pricing &amp; more details
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></a>
    </div>
</div>
                                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #DD6400">
            Resolution Voting
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Make informed decisions on crucial matters with Kudvo's Resolution Voting feature. Propose, debate, and vote on resolutions effectively to ensure clarity and transparency.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<a href="http://127.0.0.1:8000/products/resolution-voting" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Pricing &amp; more details
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></a>
    </div>
</div>
                                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #21A300">
            Survey
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Gather valuable insights and feedback from stakeholders with Kudvo's Survey tool. Conduct surveys on organizational performance, member satisfaction, and more to drive informed decision-making.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<a href="http://127.0.0.1:8000/products/survey" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Free Survey
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></a>
    </div>
</div>
                                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #E92E66">
            AGM Meeting Voting
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Streamline Annual General Meetings (AGMs) with Kudvo's AGM Meeting Voting solution. Enable remote participation and voting for attendees while maintaining the integrity of the process.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 pointer-events-none opacity-70 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg" disabled="disabled" type="button" wire:loading.attr="disabled">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Coming soon...
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></button>
    </div>
</div>
                                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #4285F7">
            Live Polling
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Engage your audience in real-time with Kudvo's Live Polling feature. Conduct interactive polls during events, webinars, or meetings to gather feedback and enhance audience interaction.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 pointer-events-none opacity-70 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg" disabled="disabled" type="button" wire:loading.attr="disabled">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Coming soon...
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></button>
    </div>
</div>
                                    <div data-aos="flip-right" class="transform rounded-2xl border border-gray-200 bg-white/80 shadow-md transition-all duration-200 hover:-translate-y-1 hover:shadow-lg aos-init">
    <div class="flex h-full flex-col justify-between p-4 font-sans sm:p-6 md:space-y-6 md:p-8">
        <h5 class="mb-2 text-center text-2xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-3xl" style="color: #8C5A85">
            Meeting Voting
        </h5>
        <p class="mb-3 text-center text-base font-normal text-gray-700 sm:text-lg">
            Make meetings more productive and inclusive with Kudvo's Meeting Voting functionality. Enable attendees to vote on agenda items, proposals, or decisions, ensuring every voice is heard.
        </p>
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600); font-family: 'Inter', system-ui, sans-serif;" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 pointer-events-none opacity-70 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid fi-btn-outlined ring-1 text-custom-600 ring-custom-600 hover:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-500 mt-2 w-full !rounded-full py-3 text-base btn-primary focus-outline sm:text-lg" disabled="disabled" type="button" wire:loading.attr="disabled">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Coming soon...
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></button>
    </div>
</div>
                <!--[if ENDBLOCK]><![endif]-->            </div>
        </div>
    </section>

    <section id="clientele" class="container space-y-6 py-16">
        <h4 class="text-center text-2xl font-semibold md:text-4xl">
            Our Happy Clients
        </h4>
        <div x-data="{}" x-init="
                $nextTick(() =&gt; {
                    let ul = $refs.items
                    ul.insertAdjacentHTML('afterend', ul.outerHTML)
                    ul.nextSibling.setAttribute('aria-hidden', 'true')
                })
            " class="my-4 inline-flex w-full flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-128px),transparent_100%)]">
            <ul x-ref="items" class="flex animate-infinite-scroll items-center justify-center md:justify-start">
                <!--[if BLOCK]><![endif]-->                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/axeman-michigan.webp" alt="Axeman Michigan" title="Axeman Michigan">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/csir.webp" alt="CSIR" title="CSIR">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/esic.webp" alt="ESIC" title="ESIC">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/fron-junior-lebanon.webp" alt="Fron Junior Lebanon" title="Fron Junior Lebanon">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/hong-yuan-international-group.webp" alt="Hong Yuan International Group" title="Hong Yuan International Group">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/international-youth-federation.webp" alt="International Youth Federation" title="International Youth Federation">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/jtf-union.webp" alt="JTF Union" title="JTF Union">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/rr-international-school.webp" alt="RR International School" title="RR International School">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/simplernow-community.webp" alt="Simplernow Community" title="Simplernow Community">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/spo-society.webp" alt="SPO Society" title="SPO Society">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/telkom-indonesia.webp" alt="Telkom Indonesia" title="Telkom Indonesia">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/uae-bangladesh-investment-group-ltd.webp" alt="UAE/Bangladesh Investment Group Ltd" title="UAE/Bangladesh Investment Group Ltd">
                    </li>
                <!--[if ENDBLOCK]><![endif]-->            </ul><ul x-ref="items" class="flex animate-infinite-scroll items-center justify-center md:justify-start" aria-hidden="true">
                <!--[if BLOCK]><![endif]-->                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/axeman-michigan.webp" alt="Axeman Michigan" title="Axeman Michigan">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/csir.webp" alt="CSIR" title="CSIR">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/esic.webp" alt="ESIC" title="ESIC">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/fron-junior-lebanon.webp" alt="Fron Junior Lebanon" title="Fron Junior Lebanon">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/hong-yuan-international-group.webp" alt="Hong Yuan International Group" title="Hong Yuan International Group">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/international-youth-federation.webp" alt="International Youth Federation" title="International Youth Federation">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/jtf-union.webp" alt="JTF Union" title="JTF Union">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/rr-international-school.webp" alt="RR International School" title="RR International School">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/simplernow-community.webp" alt="Simplernow Community" title="Simplernow Community">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/spo-society.webp" alt="SPO Society" title="SPO Society">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/telkom-indonesia.webp" alt="Telkom Indonesia" title="Telkom Indonesia">
                    </li>
                                    <li class="mx-2 md:mx-8">
                        <img loading="lazy" class="aspect-square w-16 max-w-none md:w-28" src="http://127.0.0.1:8000/img/home/clients/uae-bangladesh-investment-group-ltd.webp" alt="UAE/Bangladesh Investment Group Ltd" title="UAE/Bangladesh Investment Group Ltd">
                    </li>
                <!--[if ENDBLOCK]><![endif]-->            </ul>
        </div>
    </section>

    <section id="contact" class="h-20 rounded-t-[100%] bg-white"></section>

    <section class="!mt-0 bg-white pb-16">
        <div class="container flex flex-col items-center justify-between gap-6 md:gap-12 lg:flex-row">
            <!-- Left Section -->
            <div class="w-full flex-1 space-y-6">
                <h3 class="text-center text-2xl font-semibold md:text-start md:text-4xl">
                    Contact Us
                </h3>
                <div wire:snapshot="{&quot;data&quot;:{&quot;data&quot;:[{&quot;name&quot;:null,&quot;email&quot;:null,&quot;message&quot;:null,&quot;captcha&quot;:null},{&quot;s&quot;:&quot;arr&quot;}],&quot;componentFileAttachments&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;areFormStateUpdateHooksDisabledForTesting&quot;:false,&quot;mountedFormComponentActions&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;mountedFormComponentActionsArguments&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;mountedFormComponentActionsData&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}],&quot;mountedFormComponentActionsComponents&quot;:[[],{&quot;s&quot;:&quot;arr&quot;}]},&quot;memo&quot;:{&quot;id&quot;:&quot;asUPr8aspihVChTG2BSZ&quot;,&quot;name&quot;:&quot;contact-form&quot;,&quot;path&quot;:&quot;\/&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;release&quot;:&quot;a-a-a&quot;,&quot;children&quot;:[],&quot;scripts&quot;:[],&quot;assets&quot;:[],&quot;lazyLoaded&quot;:true,&quot;errors&quot;:[],&quot;locale&quot;:&quot;en&quot;},&quot;checksum&quot;:&quot;921f18c18c7eeb6afad53310fb4462219e8c0da98248f05fa60f1f50822438ba&quot;}" wire:effects="{&quot;returns&quot;:[null]}" wire:id="asUPr8aspihVChTG2BSZ" class="contact-form">
    <form wire:submit="submit">
        <div style="--cols-default: repeat(1, minmax(0, 1fr));" class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6" x-data="{}" x-on:form-validation-error.window="if ($event.detail.livewireId !== 'asUPr8aspihVChTG2BSZ') {
                return
            }

            $nextTick(() =&gt; {
                let error = $el.querySelector('[data-validation-error]')

                if (! error) {
                    return
                }

                let elementToExpand = error

                while (elementToExpand) {
                    elementToExpand.dispatchEvent(new CustomEvent('expand'))

                    elementToExpand = elementToExpand.parentNode
                }

                setTimeout(
                    () =&gt;
                        error.closest('[data-field-wrapper]').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'start',
                        }),
                    200,
                )
        })">
    <!--[if BLOCK]><![endif]-->        
        <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
    <!--[if BLOCK]><![endif]-->                <section x-data="{
        isCollapsed:  false ,
    }" class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="fi-section-content-ctn">
        <div class="fi-section-content p-6">
            <div style="--cols-default: repeat(1, minmax(0, 1fr));" class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6">
    <!--[if BLOCK]><![endif]-->        
        <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default]" wire:key="asUPr8aspihVChTG2BSZ.data.name.Filament\Forms\Components\TextInput">
    <!--[if BLOCK]><![endif]-->                <div data-field-wrapper="" class="fi-fo-field-wrp">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="grid gap-y-2">
        <!--[if BLOCK]><![endif]-->            <div class="flex items-center gap-x-3 justify-between ">
                <!--[if BLOCK]><![endif]-->                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.name">
    

    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
        
        Your name<!--[if BLOCK]><![endif]--><sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        <!--[if ENDBLOCK]><![endif]-->    </span>

    
</label>
                <!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]-->            <div class="grid auto-cols-fr gap-y-2">
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="fi-input-wrp-input min-w-0 flex-1">
        <input class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3" id="data.name" maxlength="50" required="required" type="text" wire:model="data.name">
    </div>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></div>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->    </div>
</div>

            <!--[if ENDBLOCK]><![endif]-->
</div>
            
        <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default]" wire:key="asUPr8aspihVChTG2BSZ.data.email.Filament\Forms\Components\TextInput">
    <!--[if BLOCK]><![endif]-->                <div data-field-wrapper="" class="fi-fo-field-wrp">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="grid gap-y-2">
        <!--[if BLOCK]><![endif]-->            <div class="flex items-center gap-x-3 justify-between ">
                <!--[if BLOCK]><![endif]-->                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.email">
    

    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
        
        Your email address<!--[if BLOCK]><![endif]--><sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        <!--[if ENDBLOCK]><![endif]-->    </span>

    
</label>
                <!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]-->            <div class="grid auto-cols-fr gap-y-2">
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="fi-input-wrp-input min-w-0 flex-1">
        <input class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3" id="data.email" maxlength="150" required="required" type="email" wire:model="data.email">
    </div>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></div>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->    </div>
</div>

            <!--[if ENDBLOCK]><![endif]-->
</div>
            
        <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default]" wire:key="asUPr8aspihVChTG2BSZ.data.message.Filament\Forms\Components\Textarea">
    <!--[if BLOCK]><![endif]-->                <div data-field-wrapper="" class="fi-fo-field-wrp">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="grid gap-y-2">
        <!--[if BLOCK]><![endif]-->            <div class="flex items-center gap-x-3 justify-between ">
                <!--[if BLOCK]><![endif]-->                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.message">
    

    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
        
        Your message<!--[if BLOCK]><![endif]--><sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        <!--[if ENDBLOCK]><![endif]-->    </span>

    
</label>
                <!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]-->            <div class="grid auto-cols-fr gap-y-2">
                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-textarea overflow-hidden">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div class="fi-input-wrp-input min-w-0 flex-1">
        <div wire:ignore.self="" style="height: 60px;">
            <textarea x-load="" x-load-src="http://127.0.0.1:8000/js/filament/forms/components/textarea.js?v=3.3.49.0" x-data="textareaFormComponent({
                            initialHeight: 3.75,
                            shouldAutosize: true,
                            state: $wire.$entangle('data.message'),
                        })" x-intersect.once="resize()" x-on:resize.window="resize()" x-model="state" class="block h-full w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 resize-none" id="data.message" minlength="30" required="required" wire:model="data.message"></textarea>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></div>

                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->            </div>
        <!--[if ENDBLOCK]><![endif]-->    </div>
</div>

            <!--[if ENDBLOCK]><![endif]-->
</div>
            
        <div style="--col-span-default: span 1 / span 1;" class="hidden" wire:key="asUPr8aspihVChTG2BSZ.data.captcha.Coderflex\FilamentTurnstile\Forms\Components\Turnstile">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
</div>
    <!--[if ENDBLOCK]><![endif]-->
</div>
        </div>

        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    </div>
</section>

            <!--[if ENDBLOCK]><![endif]-->
</div>
    <!--[if ENDBLOCK]><![endif]-->
</div>


        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-xl fi-btn-size-xl gap-1.5 px-4 py-3 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 mt-6 w-full" type="submit" wire:loading.attr="disabled" wire:target="submit">
    <!--[if BLOCK]><![endif]-->        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]-->            <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="animate-spin fi-btn-icon transition duration-75 h-5 w-5 text-white" wire:loading.delay.default="" wire:target="submit">
    <path clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill-rule="evenodd" fill="currentColor" opacity="0.2"></path>
    <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
</svg>
        <!--[if ENDBLOCK]><![endif]-->
        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->    <!--[if ENDBLOCK]><![endif]-->
    <span class="fi-btn-label">
        Submit
    </span>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]--></button>
    </form>

    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
<!--[if BLOCK]><![endif]-->    
    <form wire:submit.prevent="callMountedFormComponentAction">
        <div aria-modal="true" role="dialog" x-data="{
        isOpen: false,

        livewire: null,

        close: function () {
            this.isOpen = false

            if (! this.$refs.modalContainer.isConnected) {
                return
            }

            this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-closed', { detail: { id: 'asUPr8aspihVChTG2BSZ-form-component-action' } }),
            )
        },

        open: function () {
            this.$nextTick(() =&gt; {
                this.isOpen = true

                
                this.$refs.modalContainer.dispatchEvent(
                    new CustomEvent('modal-opened', { detail: { id: 'asUPr8aspihVChTG2BSZ-form-component-action' } }),
                )
            })
        },
    }" x-on:close-modal.window="if ($event.detail.id === 'asUPr8aspihVChTG2BSZ-form-component-action') close()" x-on:open-modal.window="if ($event.detail.id === 'asUPr8aspihVChTG2BSZ-form-component-action') open()" data-fi-modal-id="asUPr8aspihVChTG2BSZ-form-component-action" x-trap.noscroll="isOpen" x-bind:class="{
        'fi-modal-open': isOpen,
    }" class="fi-modal block">
    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
    <div x-show="isOpen" style="display: none;">
        <div aria-hidden="true" x-show="isOpen" x-transition.duration.300ms.opacity="" class="fi-modal-close-overlay fixed inset-0 z-40 bg-gray-950/50 dark:bg-gray-950/75" style="display: none;"></div>

        <div class="fixed inset-0 z-40 overflow-y-auto cursor-pointer">
            <div x-ref="modalContainer" x-on:click.self="
                        document.activeElement.selectionStart === undefined &amp;&amp;
                            document.activeElement.selectionEnd === undefined &amp;&amp;
                            $dispatch('close-modal', { id: 'asUPr8aspihVChTG2BSZ-form-component-action' })
                    " class="relative grid min-h-full grid-rows-[1fr_auto_1fr] justify-items-center sm:grid-rows-[1fr_auto_3fr] p-4" x-on:modal-closed.stop="if (!$event.detail?.id?.startsWith('asUPr8aspihVChTG2BSZ-')) {
                    return
                }

                const mountedFormComponentActionShouldOpenModal = false


                if (mountedFormComponentActionShouldOpenModal) {
                    $wire.unmountFormComponentAction(false, false)
                }">
                <div x-data="{ isShown: false }" x-init="
                        $nextTick(() =&gt; {
                            isShown = isOpen
                            $watch('isOpen', () =&gt; (isShown = isOpen))
                        })
                    " x-on:keydown.window.escape="$dispatch('close-modal', { id: 'asUPr8aspihVChTG2BSZ-form-component-action' })" x-show="isShown" x-transition:enter="duration-300" x-transition:leave="duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" wire:key="asUPr8aspihVChTG2BSZ.modal.asUPr8aspihVChTG2BSZ-form-component-action.window" class="fi-modal-window pointer-events-auto relative row-start-2 flex w-full cursor-default flex-col bg-white shadow-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mx-auto rounded-xl hidden max-w-sm" style="display: none;">
                    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->                </div>
            </div>
        </div>
    </div>
</div>
    </form>

    <!--[if ENDBLOCK]><![endif]--></div>            </div>

            <!-- Right Section -->
            <div class="relative flex aspect-video w-full flex-1 items-center justify-center lg:max-w-lg">
                <!-- Background Image -->
                <img loading="lazy" src="http://127.0.0.1:8000/img/contact-bg.webp" alt="An abstract geometric pattern with repeating blue and light purple shapes, including circles, semi-circles, and triangles, creating a mosaic-like design." title="Contact Us" class="absolute inset-0 h-full w-full object-cover sm:rounded-lg md:rounded-3xl">
                <!-- Content Card -->
                <div class="relative space-y-4 rounded-xl bg-white p-4 shadow-lg sm:p-6 md:p-8">
                    <h4 class="text-center text-2xl font-semibold md:text-4xl">
                        Kudvo
                    </h4>
                    <div class="flex items-center justify-center gap-4">
                        <!--[if BLOCK]><![endif]-->    <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"></path>
</svg><!--[if ENDBLOCK]><![endif]-->                        <div class="text-sm sm:text-lg">
                            <span class="hidden sm:inline">
                                Call / Whatsapp
                            </span>
                            <a href="tel:+1-631-731-3526" class="cursor-pointer text-nowrap hover:text-primary-700 hover:underline">
                                +1-631-731-3526
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-4">
                        <!--[if BLOCK]><![endif]-->    <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path>
</svg><!--[if ENDBLOCK]><![endif]-->                        <a href="mailto:hello@kudvo.com" class="cursor-pointer text-sm hover:text-primary-700 hover:underline sm:text-lg">
                            hello@kudvo.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection