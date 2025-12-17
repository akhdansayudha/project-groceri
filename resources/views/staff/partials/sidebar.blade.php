<aside
    class="w-72 bg-[#0a0a0a] text-gray-400 flex flex-col border-r border-gray-800 shrink-0 h-screen sticky top-0 transition-all">

    {{-- LOGO --}}
    <div class="h-20 flex items-center px-8 border-b border-gray-800 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <span class="font-bold text-black text-lg">V.</span>
            </div>
            <div>
                <a href="{{ route('staff.dashboard') }}"
                    class="text-xl font-bold tracking-tighter text-white block leading-none">
                    Vektora<span class="text-white">.</span>
                </a>
                <span class="text-[10px] text-gray-500 font-medium tracking-wide uppercase">Staff Portal</span>
            </div>
        </div>
    </div>

    {{-- MENU --}}
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">

        {{-- GROUP: WORKSPACE (Fokus Pengerjaan) --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Workspace
            </p>
            <div class="space-y-1">
                {{-- Dashboard --}}
                <a href="{{ route('staff.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.dashboard') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="grid" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Overview</span>
                </a>

                {{-- My Tasks (Active) --}}
                {{-- Menggunakan staff.projects.index sebagai acuan aktif --}}
                <a href="{{ route('staff.projects.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.projects.index') || request()->routeIs('staff.projects.show') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <div class="flex items-center gap-3 flex-1">
                        <i data-feather="briefcase" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                        <span>Active Projects</span>
                    </div>
                    @if (isset($stats['tasks_active']) && $stats['tasks_active'] > 0)
                        <span
                            class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-blue-900/50">
                            {{ $stats['tasks_active'] }}
                        </span>
                    @endif
                </a>

                {{-- Project History (Completed) --}}
                <a href="{{ route('staff.projects.history') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.projects.history') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="clock" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Project History</span>
                </a>
            </div>
        </div>

        {{-- GROUP: GROWTH & FINANCE (Fokus Karir & Uang) --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Career & Finance
            </p>
            <div class="space-y-1">
                {{-- Performance Report --}}
                <a href="{{ route('staff.performance.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.performance.*') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="bar-chart-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>My Performance</span>
                </a>

                {{-- Earnings / Payouts --}}
                <a href="{{ route('staff.finance.earnings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.finance.*') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="dollar-sign" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Earnings & Payouts</span>
                </a>
            </div>
        </div>

        {{-- GROUP: SYSTEM (Pengaturan) --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> System
            </p>
            <div class="space-y-1">
                {{-- Settings --}}
                <a href="{{ route('staff.settings') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('staff.settings') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="settings" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Account Settings</span>
                </a>
            </div>
        </div>
    </div>

    {{-- PROFILE BOTTOM --}}
    <div class="p-6 border-t border-gray-800 bg-[#050505] shrink-0">
        <div class="flex items-center gap-4">
            <div
                class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold text-sm ring-2 ring-gray-800">
                {{ substr(Auth::user()->full_name, 0, 2) }}
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->full_name }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] text-gray-500 truncate uppercase tracking-wider">Online</p>
                </div>
            </div>
            <form action="{{ route('staff.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="p-2 rounded-lg text-gray-500 hover:bg-red-500/10 hover:text-red-500 transition-colors"
                    title="Sign Out">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
