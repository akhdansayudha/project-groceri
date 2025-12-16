@php
    // Logic sederhana untuk menghitung user online (Last Activity < 5 menit lalu)
    // Menggunakan DB Facade langsung di view untuk kepraktisan tanpa ubah Controller global
    $onlineStaff = \Illuminate\Support\Facades\DB::table('sessions')
        ->join('users', 'sessions.user_id', '=', 'users.id')
        ->where('users.role', 'staff')
        ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
        ->count();

    $onlineClients = \Illuminate\Support\Facades\DB::table('sessions')
        ->join('users', 'sessions.user_id', '=', 'users.id')
        ->where('users.role', 'client')
        ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
        ->count();
@endphp

<header
    class="sticky top-0 z-40 w-full px-8 py-4 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 transition-all duration-300">
    <div class="flex items-center justify-between">

        {{-- LEFT: DATE --}}
        <div class="flex items-center gap-4">
            <span class="hidden lg:block text-xs font-medium text-gray-400">
                {{ now()->format('l, d M Y') }}
            </span>
        </div>

        {{-- RIGHT: ACTIONS --}}
        <div class="flex items-center gap-4 md:gap-6">

            {{-- 1. ONLINE MONITOR BADGES --}}
            <div class="hidden md:flex items-center gap-3">

                {{-- Staff Online Badge --}}
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-purple-50 border border-purple-100 text-purple-700 shadow-sm select-none transition-transform hover:scale-105"
                    title="Staff Currently Online">
                    <div class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-widest">
                        {{ $onlineStaff }} Staff
                    </span>
                </div>

                {{-- Client Online Badge --}}
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-50 border border-green-100 text-green-700 shadow-sm select-none transition-transform hover:scale-105"
                    title="Clients Currently Online">
                    <div class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-widest">
                        {{ $onlineClients }} Clients
                    </span>
                </div>

            </div>

            {{-- Separator --}}
            <div class="h-6 w-[1px] bg-gray-200 hidden md:block"></div>

            {{-- 2. VIEW SITE LINK --}}
            <a href="{{ route('home') }}" target="_blank"
                class="group flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-black transition-colors">
                <i data-feather="external-link" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                <span class="hidden sm:inline">View Site</span>
            </a>

            {{-- Separator Profile --}}
            <div class="h-8 w-[1px] bg-gray-100 mx-1 hidden md:block"></div>

            {{-- 3. ADMIN PROFILE (Agar seimbang dengan Header Client) --}}
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-800 leading-none">
                        {{ Auth::user()->full_name }}
                    </p>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mt-1">
                        Administrator
                    </p>
                </div>

                <div class="relative">
                    <div class="w-10 h-10 rounded-full p-[2px] bg-gradient-to-br from-gray-800 to-black">
                        <div
                            class="w-full h-full rounded-full overflow-hidden bg-white border border-white flex items-center justify-center">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover">
                            @else
                                <span class="font-bold text-black">{{ substr(Auth::user()->full_name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    {{-- Admin Online Dot --}}
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-blue-500 border-2 border-white rounded-full">
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
