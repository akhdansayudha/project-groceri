@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">System Audit Logs</h1>
            <p class="text-gray-500 text-sm">Rekam jejak aktivitas pengguna untuk keamanan dan pemantauan.</p>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 fade-in">
        {{-- Card 1: Today's Activity --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <i data-feather="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Activities Today</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_count']) }}</h3>
            </div>
        </div>

        {{-- Card 2: Active Users --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
                <i data-feather="users" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Users Today</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['unique_users']) }}</h3>
            </div>
        </div>

        {{-- Card 3: Security Info (Static/Dummy for Visual Balance) --}}
        <div
            class="bg-black text-white p-5 rounded-3xl shadow-lg shadow-black/10 col-span-1 lg:col-span-2 relative overflow-hidden flex items-center justify-between">
            <div class="relative z-10">
                <p class="text-xs font-bold text-gray-400 uppercase mb-1">System Status</p>
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Logging Active
                </h3>
                <p class="text-[10px] text-gray-500 mt-1">All actions are being recorded securely.</p>
            </div>
            <i data-feather="shield" class="w-16 h-16 text-gray-800 absolute -right-2 -bottom-4 opacity-50"></i>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in flex flex-col">

        {{-- Toolbar --}}
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-white">
            <div class="flex items-center gap-2 w-full md:w-auto">
                {{-- Filter Action --}}
                <div class="relative">
                    <form id="filterForm" action="{{ route('admin.audit.index') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="action" onchange="document.getElementById('filterForm').submit()"
                            class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl py-2.5 pl-4 pr-10 focus:outline-none focus:border-black cursor-pointer hover:bg-gray-100 transition-colors">
                            <option value="all">All Actions</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                    {{ ucfirst($act) }}
                                </option>
                            @endforeach
                        </select>
                        <i data-feather="chevron-down"
                            class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </form>
                </div>

                {{-- Reset Button --}}
                @if (request('action') || request('search'))
                    <a href="{{ route('admin.audit.index') }}"
                        class="p-2.5 bg-gray-100 rounded-xl text-gray-500 hover:bg-gray-200 hover:text-black transition-colors"
                        title="Reset Filters">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            {{-- Search --}}
            <form action="{{ route('admin.audit.index') }}" method="GET" class="relative group w-full md:w-80">
                <input type="hidden" name="action" value="{{ request('action') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search user, IP, or description..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-xs font-bold focus:outline-none focus:border-black focus:bg-white transition-all placeholder-gray-400">
                <i data-feather="search"
                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 group-focus-within:text-black transition-colors"></i>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Details / Description</th>
                        <th class="px-6 py-4 text-right">Technical Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($logs as $log)
                        {{-- Logic Warna Badge --}}
                        @php
                            $badgeColor = 'bg-gray-100 text-gray-600 border-gray-200'; // Default
                            $act = strtolower($log->action);

                            if (in_array($act, ['login', 'create', 'store', 'upload'])) {
                                $badgeColor = 'bg-green-50 text-green-700 border-green-200';
                            } elseif (in_array($act, ['logout'])) {
                                $badgeColor = 'bg-gray-100 text-gray-500 border-gray-200';
                            } elseif (in_array($act, ['update', 'edit', 'adjustment'])) {
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (in_array($act, ['delete', 'destroy', 'remove'])) {
                                $badgeColor = 'bg-red-50 text-red-700 border-red-200';
                            } elseif (in_array($act, ['warning', 'error'])) {
                                $badgeColor = 'bg-orange-50 text-orange-700 border-orange-200';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            {{-- Timestamp --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</p>
                            </td>

                            {{-- User --}}
                            <td class="px-6 py-4">
                                @if ($log->user)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200 shrink-0">
                                            <img src="{{ $log->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($log->user->full_name) }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">
                                                {{ Str::limit($log->user->full_name, 18) }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wide">
                                                {{ $log->user->role }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 opacity-60">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i data-feather="user-x" class="w-4 h-4 text-gray-400"></i>
                                        </div>
                                        <span class="text-xs font-bold text-gray-500 italic">Deleted User</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Action Badge --}}
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4">
                                <p class="text-gray-600 font-medium text-xs leading-relaxed max-w-sm">
                                    {{ $log->description }}
                                </p>
                            </td>

                            {{-- Technical Info (IP & Agent) --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span
                                        class="font-mono text-[10px] font-bold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                        {{ $log->ip_address }}
                                    </span>
                                    {{-- Tooltip style for User Agent --}}
                                    <div class="group/ua relative">
                                        <span
                                            class="text-[10px] text-gray-400 cursor-help border-b border-dashed border-gray-300">
                                            Browser Details
                                        </span>
                                        <div
                                            class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-black text-white text-[9px] rounded-lg shadow-lg opacity-0 group-hover/ua:opacity-100 transition-opacity pointer-events-none z-10">
                                            {{ $log->user_agent }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 bg-gray-50 rounded-full border border-gray-100">
                                        <i data-feather="shield" class="w-8 h-8 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada log aktivitas tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
