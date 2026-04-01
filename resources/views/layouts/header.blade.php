<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            <div class="flex-shrink-0 flex items-center gap-2">
                <img class="h-10 w-auto" src="apple-touch-icon.png" alt="Kudvo Logo">
               <a href="{{ route('home') }}"> <span class="text-2xl font-bold text-gray-900">Kudvo</span>
            </a>
            </div>

            <nav class="hidden md:flex space-x-8 items-center">
                <div class="relative group">
                    <button class="text-gray-600 group-hover:text-gray-900 px-3 py-2 text-sm font-medium flex items-center gap-1">
                        Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
                <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Clientele</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Wiki</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Contact</a>
                <a href="#" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">Help</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="/login" class="px-5 py-2 text-sm font-semibold text-white bg-green-500 hover:bg-green-600 rounded-md transition duration-150 ease-in-out">
                    Sign In
                </a>
                <a href="/register" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition duration-150 ease-in-out">
                    Sign Up
                </a>
            </div>

        </div>
    </div>
</header>