<aside
    class="w-72 bg-[#0a0a0a] text-gray-400 flex flex-col border-r border-gray-800 shrink-0 transition-all duration-300 h-screen sticky top-0">

    {{-- LOGO AREA --}}
    <div class="h-20 flex items-center px-8 border-b border-gray-800 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <span class="font-bold text-black text-lg">V.</span>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}"
                    class="text-xl font-bold tracking-tighter text-white block leading-none">
                    Vektora<span class="text-blue-500">.</span>
                </a>
                <span class="text-[10px] text-gray-500 font-medium tracking-wide uppercase">Control Tower</span>
            </div>
        </div>
    </div>

    {{-- MENU SCROLL AREA --}}
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8 custom-scrollbar">

        {{-- GROUP 1: AGENCY OVERVIEW --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Agency
            </p>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="grid" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.analytics') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.analytics') ? 'bg-white text-black font-bold shadow-[0_0_20px_rgba(255,255,255,0.1)]' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="bar-chart-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Analytics & Reports</span>
                </a>
            </div>
        </div>

        {{-- GROUP 2: PRODUCTION (DISEDERHANAKAN) --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Production
            </p>
            <div class="space-y-1">
                {{-- MENU GABUNGAN: PROJECTS --}}
                <a href="{{ route('admin.projects.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.projects.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <div class="flex items-center gap-3 flex-1">
                        <i data-feather="layers" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                        <span>All Projects</span>
                    </div>

                    {{-- Badge untuk menghitung project yang statusnya Queue (Pending) --}}
                    @php
                        $queueCount = \App\Models\Task::where('status', 'queue')->count();
                    @endphp
                    @if ($queueCount > 0)
                        <span
                            class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-blue-900/50 animate-pulse">
                            {{ $queueCount }} New
                        </span>
                    @endif
                </a>
            </div>
        </div>

        {{-- GROUP 3: WORKSPACE MONITORING --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Live Monitoring
            </p>
            <div class="space-y-1">
                <a href="{{ route('admin.workspaces.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.workspaces.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="monitor" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Browse Workspaces</span>
                </a>

                {{-- DYNAMIC SHORTCUTS: 3 NEWEST WORKSPACES --}}
                @php
                    $recentWorkspaces = \App\Models\Workspace::orderBy('created_at', 'desc')->take(3)->get();
                    $totalWorkspaces = \App\Models\Workspace::count();
                    $remainingCount = max(0, $totalWorkspaces - 3);
                @endphp

                <div class="mt-2 pl-3 border-l border-gray-800 ml-5 space-y-2">
                    @foreach ($recentWorkspaces as $ws)
                        {{-- Link ke Detail Workspace (Pastikan route show tersedia, jika belum arahkan ke index) --}}
                        <a href="{{ Route::has('admin.workspaces.show') ? route('admin.workspaces.show', $ws->id) : route('admin.workspaces.index') }}"
                            class="flex items-center gap-2 text-xs text-gray-500 hover:text-white group transition-colors"
                            title="{{ $ws->name }}">
                            {{-- Indikator: Workspace paling baru akan berkedip (Pulse) --}}
                            <div
                                class="w-2 h-2 rounded-full bg-green-500 {{ $loop->first ? 'animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'opacity-70' }}">
                            </div>
                            <span class="truncate w-32">{{ $ws->name }}</span>
                        </a>
                    @endforeach

                    {{-- Indikator Sisa Workspace --}}
                    @if ($remainingCount > 0)
                        <a href="{{ route('admin.workspaces.index') }}"
                            class="flex items-center gap-2 text-[10px] text-gray-600 hover:text-gray-400 group transition-colors pl-4 pt-1">
                            <span>+{{ $remainingCount }} other workspaces</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- GROUP 4: MANAGEMENT --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> People & Services
            </p>
            <div class="space-y-1">
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.users.*') ? 'bg-white text-black font-bold' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="users" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Client Database</span>
                </a>

                <a href="{{ route('admin.staff.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.staff.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="briefcase" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Staff & Roles</span>
                </a>

                <a href="{{ route('admin.performance.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.performance.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="trending-up" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Performance & Payroll</span>
                </a>

                <a href="{{ route('admin.services.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.services.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="package" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Services & Pricing</span>
                </a>
            </div>
        </div>

        {{-- GROUP 5: FINANCE --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Finance
            </p>
            <div class="space-y-1">
                <a href="{{ route('admin.invoices.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.invoices.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="file-text" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Invoices</span>
                </a>

                <a href="{{ route('admin.tokens.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.tokens.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="database" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Token Manager</span>
                </a>
            </div>
        </div>

        {{-- GROUP 6: SYSTEM --}}
        <div>
            <p class="px-3 text-[10px] font-bold uppercase tracking-widest mb-3 opacity-50 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> System
            </p>
            <div class="space-y-1">
                <a href="{{ route('admin.notifications.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.notifications.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="bell" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Broadcast Notif</span>
                </a>

                <a href="{{ route('admin.audit.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.audit.*') ? 'bg-white text-black font-bold shadow-lg' : 'hover:bg-gray-900 hover:text-white' }}">
                    <i data-feather="activity" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Audit Logs</span>
                </a>
            </div>
        </div>
    </div>

    {{-- USER PROFILE BOTTOM --}}
    <div class="p-6 border-t border-gray-800 bg-[#050505] shrink-0">
        <div class="flex items-center gap-4">
            <div
                class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold text-sm ring-2 ring-gray-800">
                AD
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->full_name ?? 'Administrator' }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-[10px] text-gray-500 truncate uppercase tracking-wider">Super Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
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
