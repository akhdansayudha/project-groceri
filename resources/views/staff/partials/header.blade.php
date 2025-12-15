<header class="h-20 bg-white/50 backdrop-blur-xl border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-30">
    <div class="flex items-center gap-4">
            {{-- Bisa diisi breadcrumb navigation di masa depan --}}
            <div class="hidden lg:block h-6 w-[1px] bg-gray-200"></div>
            <span class="hidden lg:block text-xs font-medium text-gray-400">
                {{ now()->format('l, d M Y') }}
            </span>
        </div>

    <div class="flex items-center gap-6">
        {{-- Token Balance Card --}}
        <div class="flex items-center gap-3 px-4 py-2 bg-black text-white rounded-full shadow-lg shadow-gray-200">
            <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
            <div class="flex items-baseline gap-1">
                <span class="font-bold text-sm">{{ Auth::user()->wallet->balance ?? 0 }}</span>
                <span class="text-[10px] font-medium text-gray-400">TX</span>
            </div>
        </div>
    </div>
</header>