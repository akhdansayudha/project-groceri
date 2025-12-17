@php
    $user = Auth::user();
    $tierName = $user->wallet->tier->name ?? 'Starter';
    $balance = $user->wallet->balance ?? 0;

    // Default Style (Starter)
    $badgeTheme = [
        'bg' => 'bg-gray-50',
        'border' => 'border-gray-200',
        'text' => 'text-gray-600',
        'icon' => 'shield',
        'effect' => '',
    ];

    // Logic Switch Style
    if (stripos($tierName, 'Professional') !== false) {
        $badgeTheme = [
            'bg' => 'bg-indigo-50',
            'border' => 'border-indigo-200',
            'text' => 'text-indigo-700',
            'icon' => 'star',
            'effect' => 'shadow-sm shadow-indigo-100',
        ];
    } elseif (stripos($tierName, 'Ultimate') !== false) {
        $badgeTheme = [
            'bg' => 'bg-gradient-to-r from-amber-50 via-yellow-50 to-amber-50',
            'border' => 'border-amber-200',
            'text' => 'text-amber-700',
            'icon' => 'award',
            'effect' => 'shadow-md shadow-amber-500/20 ring-1 ring-amber-100',
        ];
    }
@endphp

<header
    class="sticky top-0 z-40 w-full px-8 py-4 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 transition-all duration-300">
    <div class="flex items-center justify-between">

        <div class="flex items-center gap-4">

            {{-- TOMBOL BURGER MENU (BARU) --}}
            <button id="sidebar-toggle"
                class="p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-black transition-colors focus:outline-none">
                <i data-feather="menu" class="w-5 h-5"></i>
            </button>

            <div class="h-6 w-[1px] bg-gray-200"></div>

            <span class="hidden lg:block text-xs font-medium text-gray-400">
                {{ now()->format('l, d M Y') }}
            </span>
        </div>

        <div class="flex items-center gap-4 md:gap-6">

            <div class="hidden md:flex items-center gap-3">

                {{-- DYNAMIC TIER BADGE --}}
                <div
                    class="flex items-center gap-2 px-3 py-1.5 rounded-full border {{ $badgeTheme['bg'] }} {{ $badgeTheme['border'] }} {{ $badgeTheme['text'] }} {{ $badgeTheme['effect'] }} transition-all select-none">

                    @if (stripos($tierName, 'Ultimate') !== false)
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-crown">
                            <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14" />
                        </svg>
                    @elseif(stripos($tierName, 'Professional') !== false)
                        <i data-feather="star" class="w-3.5 h-3.5 fill-indigo-100"></i>
                    @else
                        <i data-feather="shield" class="w-3.5 h-3.5"></i>
                    @endif

                    <span class="text-[11px] font-extrabold uppercase tracking-widest">
                        {{ $tierName }} Member
                    </span>
                </div>

                {{-- TOKEN BALANCE --}}
                <a href="{{ route('client.wallet.index') }}"
                    class="group flex items-center gap-3 px-4 py-1.5 bg-[#0a0a0a] text-white rounded-full border border-gray-800 shadow-lg shadow-gray-200/50 hover:shadow-xl hover:scale-[1.02] transition-all cursor-pointer">

                    <div
                        class="flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-br from-yellow-300 to-yellow-500 text-black shadow-inner">
                        <i data-feather="zap" class="w-3 h-3 fill-black"></i>
                    </div>

                    <div class="flex items-baseline gap-1.5 pr-1">
                        <span class="font-bold text-sm tracking-tight">{{ number_format($balance) }}</span>
                        <span
                            class="text-[10px] font-bold text-gray-400 group-hover:text-yellow-400 transition-colors">TX</span>
                    </div>
                </a>

            </div>

            <div class="h-8 w-[1px] bg-gray-100 mx-1 hidden md:block"></div>

            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-800 leading-none group-hover:text-black transition-colors">
                        {{ $user->full_name }}
                    </p>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mt-1">
                        {{ $user->role ?? 'Client' }}
                    </p>
                </div>

                <div class="relative">
                    <div
                        class="w-10 h-10 rounded-full p-[2px] bg-gradient-to-br from-gray-100 to-gray-200 group-hover:from-black group-hover:to-gray-600 transition-all">
                        <div class="w-full h-full rounded-full overflow-hidden bg-white border border-white">
                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=random' }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
