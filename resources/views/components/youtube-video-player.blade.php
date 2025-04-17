<!-- resources/views/components/youtube-video.blade.php -->
@props([
    'videoId',
    'title',
    'thumbnailQuality' => 'maxresdefault',
])

<div
    class="[&_[x-cloak]]:hidden"
    x-data="{
        modalOpen: false,
        videoId: '{{ $videoId }}',
        thumbnailUrl: 'https://img.youtube.com/vi/{{ $videoId }}/{{ $thumbnailQuality }}.jpg',
        iframeSrc: '',
        openModal() {
            this.modalOpen = true;
            this.iframeSrc = 'https://www.youtube.com/embed/' + this.videoId + '?autoplay=1&rel=0';
        },
        closeModal() {
            this.modalOpen = false;
            this.iframeSrc = ''; // Clear the src to stop video playback
            document.exitFullscreen();
        },
        toggleFullscreen() {
            const modal = document.getElementById('youtube-modal-container');
            if (modal) {
                if (!document.fullscreenElement) {
                    modal.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            }
        }
    }"
>
    <!-- Video thumbnail -->
    <button
        class="group relative flex items-center justify-center rounded-3xl focus:outline-none focus-visible:ring focus-visible:ring-indigo-300"
        @click="openModal()"
        aria-controls="youtube-modal"
        aria-label="Watch the video"
    >
        <img
            class="w-full h-auto rounded-3xl transition-transform duration-300 hover:scale-105"
            x-bind:src="thumbnailUrl"
            alt="{{ $title }}"
        />
        <!-- Play icon -->
        <svg
            class="pointer-events-none absolute transition-transform duration-300 ease-in-out group-hover:scale-110"
            xmlns="http://www.w3.org/2000/svg"
            width="72"
            height="72"
        >
            <circle
                class="fill-white"
                cx="36"
                cy="36"
                r="36"
                fill-opacity=".8"
            />
            <path
                class="fill-indigo-500 drop-shadow-2xl"
                d="M44 36a.999.999 0 0 0-.427-.82l-10-7A1 1 0 0 0 32 29V43a.999.999 0 0 0 1.573.82l10-7A.995.995 0 0 0 44 36V36c0 .001 0 .001 0 0Z"
            />
        </svg>
    </button>
    <!-- End: Video thumbnail -->

    <!-- Modal backdrop -->
    <div
        class="fixed inset-0 z-[99999] bg-black bg-opacity-75 transition-opacity"
        x-show="modalOpen"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-100 ease-out"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
        x-cloak
    ></div>
    <!-- End: Modal backdrop -->

    <!-- Modal dialog -->
    <div
        id="youtube-modal"
        class="fixed inset-0 z-[99999] flex items-center justify-center"
        role="dialog"
        aria-modal="true"
        x-show="modalOpen"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="scale-75 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition duration-200 ease-out"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-75 opacity-0"
        @keydown.escape.window="closeModal()"
        x-cloak
    >
        <div
            id="youtube-modal-container"
            class="w-full h-full max-w-6xl max-h-[90vh] bg-black shadow-2xl"
            @click.outside="closeModal()"
        >
            <div class="relative w-full h-full">
                <!-- Control buttons -->
                <div class="absolute top-4 right-4 z-10 flex space-x-2">
                    <!-- Fullscreen button -->
                    <button
                        @click="toggleFullscreen()"
                        class="bg-black bg-opacity-50 rounded-full p-2 text-white hover:bg-opacity-75 transition-all"
                        aria-label="Toggle fullscreen"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                        </svg>
                    </button>

                    <!-- Close button -->
                    <button
                        @click="closeModal()"
                        class="bg-black bg-opacity-50 rounded-full p-2 text-white hover:bg-opacity-75 transition-all"
                        aria-label="Close modal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- YouTube iframe with dynamic src -->
                <iframe
                    x-bind:src="iframeSrc"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </div>
    <!-- End: Modal dialog -->
</div>
