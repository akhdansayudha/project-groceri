<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8 sticky top-0 z-30">

    {{-- LEFT: BREADCRUMB / DATE --}}
    <div>
        <p class="text-xs text-gray-400">{{ now()->format('l, d F Y') }}</p>
    </div>

    {{-- RIGHT: ACTIONS --}}
    <div class="flex items-center gap-4">
        {{-- Notification Bell --}}
        <button class="relative p-2 text-gray-400 hover:text-black transition-colors">
            <i data-feather="bell" class="w-5 h-5"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
        </button>
        
        <div class="h-6 w-[1px] bg-gray-200"></div>

        <a href="{{ route('home') }}" target="_blank"
            class="text-xs font-bold text-gray-500 hover:text-black flex items-center gap-1">
            <i data-feather="external-link" class="w-3 h-3"></i> View Site
        </a>
    </div>
</header>
