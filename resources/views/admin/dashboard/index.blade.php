@extends('admin.layouts.app')

@section('content')
    {{-- WELCOME BANNER --}}
    <div class="bg-black text-white p-8 rounded-3xl mb-8 relative overflow-hidden fade-in group">
        {{-- Dekorasi Glow --}}
        <div class="absolute right-0 top-0 w-64 h-64 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>

        {{-- Dekorasi Ikon Pojok Kanan (New) --}}
        <div
            class="absolute right-6 top-6 opacity-10 transform rotate-12 group-hover:scale-110 transition-transform duration-500">
            <i data-feather="zap" class="w-24 h-24 text-white"></i>
        </div>

        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-2">Hello, {{ Auth::user()->full_name ?? 'Admin' }}! 👋</h1>
            <p class="text-gray-400 max-w-xl">
                Ada <strong class="text-white">{{ $stats['pending_projects'] }} project baru</strong> yang menunggu untuk
                ditinjau dan di-assign ke staff hari ini.
            </p>
            <div class="mt-6 flex gap-3">
                {{-- Button Review Queue --}}
                <a href="{{ route('admin.projects.index', ['status' => 'queue']) }}"
                    class="bg-white text-black px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors flex items-center gap-2">
                    Review Queue
                </a>

                {{-- Button View Reports --}}
                <a href="{{ route('admin.analytics') }}"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-300 border border-gray-700 hover:text-white hover:border-white transition-colors flex items-center gap-2">
                    View Reports
                </a>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 fade-in">
        {{-- Card 1: Revenue --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                    <i data-feather="dollar-sign" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Revenue (Monthly)</p>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</h3>
        </div>

        {{-- Card 2: Active Projects --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <i data-feather="layers" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Active Projects</p>
            </div>
            <h3 class="text-2xl font-bold">{{ $stats['active_projects'] }}</h3>
        </div>

        {{-- Card 3: Pending --}}
        <div class="bg-white p-6 rounded-3xl border border-yellow-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-10">
                <i data-feather="alert-circle" class="w-16 h-16 text-yellow-500"></i>
            </div>
            <div class="flex items-center gap-3 mb-2 relative z-10">
                <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                    <i data-feather="inbox" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Queue / Pending</p>
            </div>
            <h3 class="text-2xl font-bold text-yellow-600 relative z-10">{{ $stats['pending_projects'] }}</h3>
        </div>

        {{-- Card 4: Clients --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <i data-feather="users" class="w-4 h-4"></i>
                </div>
                <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Total Clients</p>
            </div>
            <h3 class="text-2xl font-bold">{{ $stats['total_clients'] }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- INCOMING PROJECTS (Priority) --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></div>
                    <h3 class="font-bold">Incoming Requests (Queue)</h3>
                </div>
                {{-- Link View All --}}
                <a href="{{ route('admin.projects.index') }}"
                    class="text-xs font-bold text-gray-400 hover:text-black transition-colors">
                    View All
                </a>
            </div>

            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Project Title</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($recentProjects as $task)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <span
                                        class="font-bold text-gray-900 text-xs">{{ Str::limit($task->user->full_name, 15) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700">
                                {{ Str::limit($task->title, 25) }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $task->service->name }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{-- Link Review ke Detail Project --}}
                                <a href="{{ route('admin.projects.show', $task->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-black text-white rounded-lg text-xs font-bold hover:bg-gray-800 shadow-lg shadow-black/20 transition-all">
                                    Review <i data-feather="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="check-circle" class="w-8 h-8 mb-2 text-green-500"></i>
                                    <p>All caught up! No pending requests.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- STAFF LIST (Team Overview) --}}
        <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold">Team Overview</h3>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @forelse($staffMembers as $staff)
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                <img src="{{ $staff->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $staff->full_name }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ $staff->full_name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-medium">
                                    @if ($staff->is_currently_online)
                                        {{-- JIKA ONLINE --}}
                                        <span class="text-green-600">
                                            Logged in
                                            {{ $staff->last_login_at ? \Carbon\Carbon::parse($staff->last_login_at)->diffForHumans() : 'Just now' }}
                                        </span>
                                    @else
                                        {{-- JIKA OFFLINE --}}
                                        @if ($staff->last_logout_at)
                                            {{-- Tampilkan waktu logout jika ada --}}
                                            Logged out {{ \Carbon\Carbon::parse($staff->last_logout_at)->diffForHumans() }}
                                        @elseif($staff->last_login_at)
                                            {{-- Fallback: Tampilkan login terakhir jika data logout tidak ada --}}
                                            Last seen {{ \Carbon\Carbon::parse($staff->last_login_at)->diffForHumans() }}
                                        @else
                                            Never logged in
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Online/Offline Badge --}}
                        @if ($staff->is_currently_online)
                            <div class="flex items-center gap-1.5 px-2 py-1 bg-green-50 rounded-md border border-green-100">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-[9px] font-bold text-green-600 uppercase">Online</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-100 rounded-md border border-gray-200">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-[9px] font-bold text-gray-500 uppercase">Offline</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-400">Belum ada staff.</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-gray-100">
                {{-- Link Add Staff --}}
                <a href="{{ route('admin.staff.create') }}"
                    class="block w-full text-center py-3 border border-dashed border-gray-300 rounded-xl text-xs font-bold text-gray-400 hover:border-black hover:text-black hover:bg-gray-50 transition-all">
                    + Add New Member
                </a>
            </div>
        </div>
    </div>
@endsection
