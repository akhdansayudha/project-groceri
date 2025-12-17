@php
    $user = Auth::user();
    $balance = $user->wallet->balance ?? 0;

    // Logic: Cek apakah ada Admin yang sedang Online
    $adminOnline = \Illuminate\Support\Facades\DB::table('sessions')
        ->join('users', 'sessions.user_id', '=', 'users.id')
        ->where('users.role', 'admin')
        ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
        ->exists();
@endphp

<header
    class="sticky top-0 z-40 w-full px-8 py-4 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 transition-all duration-300">
    <div class="flex items-center justify-between">

        {{-- LEFT: TOGGLE & DATE --}}
        <div class="flex items-center gap-4">

            {{-- TOMBOL BURGER MENU (BARU) --}}
            <button id="sidebar-toggle"
                class="p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-black transition-colors focus:outline-none">
                <i data-feather="menu" class="w-5 h-5"></i>
            </button>

            <div class="h-6 w-[1px] bg-gray-200 hidden lg:block"></div>

            <span class="hidden lg:block text-xs font-medium text-gray-400">
                {{ now()->format('l, d M Y') }}
            </span>
        </div>

        {{-- RIGHT: ACTIONS --}}
        <div class="flex items-center gap-4 md:gap-6">

            <div class="hidden md:flex items-center gap-3">

                {{-- 1. ADMIN MONITOR BADGE --}}
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 shadow-sm select-none transition-transform hover:scale-105"
                    title="Status Supervisor/Admin">
                    <div class="relative flex h-2 w-2">
                        @if ($adminOnline)
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        @else
                            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-[11px] font-bold tracking-widest">
                        {{ $adminOnline ? 'Admin Online' : 'Admin Offline' }}
                    </span>
                </div>

                {{-- 2. TOKEN BALANCE --}}
                <a href="{{ route('staff.finance.earnings') }}"
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

            {{-- Separator --}}
            <div class="h-8 w-[1px] bg-gray-100 mx-1 hidden md:block"></div>

            {{-- 3. STAFF PROFILE --}}
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-800 leading-none">
                        {{ $user->full_name }}
                    </p>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mt-1">
                        Staff Member
                    </p>
                </div>

                <div class="relative">
                    <div class="w-10 h-10 rounded-full p-[2px] bg-gradient-to-br from-purple-100 to-purple-300">
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
