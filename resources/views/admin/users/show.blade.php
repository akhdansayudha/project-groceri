@extends('admin.layouts.app')

@section('content')
    {{-- HEADER --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Client Profile</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <span>ID: {{ $user->id }}</span>
                    <span>•</span>
                    <span>Member since {{ $user->created_at->format('F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- KOLOM KIRI: PROFILE CARD --}}
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm text-center relative overflow-hidden">
                {{-- Status Banner --}}
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-gray-100 to-gray-200"></div>

                <div class="relative z-10">
                    <div class="relative inline-block">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $user->full_name }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-lg mx-auto mb-4 object-cover">

                        {{-- Online Status Badge --}}
                        @if ($isOnline)
                            <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-4 border-white rounded-full animate-pulse"
                                title="Online Now"></div>
                        @else
                            <div class="absolute bottom-1 right-1 w-5 h-5 bg-gray-400 border-4 border-white rounded-full"
                                title="Offline"></div>
                        @endif
                    </div>

                    <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                    <p class="text-gray-500 text-sm mb-6">{{ $user->email }}</p>

                    <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-gray-400">Current Balance</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($user->wallet->balance ?? 0) }}</p>
                            <p class="text-[10px] text-gray-400">Tokens</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-gray-400">Lifetime Spent</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_spent']) }}</p>
                            <p class="text-[10px] text-gray-400">Tokens</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="mailto:{{ $user->email }}"
                            class="w-full py-3 border border-gray-200 rounded-xl flex items-center justify-center gap-2 font-bold text-sm hover:bg-black hover:text-white hover:border-black transition-all">
                            <i data-feather="mail" class="w-4 h-4"></i> Send Email
                        </a>
                    </div>
                </div>
            </div>

            {{-- Workspace List --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-feather="folder" class="w-4 h-4"></i> Workspaces
                </h3>
                <div class="space-y-3">
                    @forelse($user->workspaces as $ws)
                        <a href="{{ route('admin.workspaces.show', $ws->id) }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-all group">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center">
                                    <i data-feather="folder" class="w-4 h-4 fill-current"></i>
                                </div>
                                <span
                                    class="text-sm font-bold text-gray-700 group-hover:text-black">{{ $ws->name }}</span>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-black"></i>
                        </a>
                    @empty
                        <p class="text-xs text-gray-400 italic">No workspaces created.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATS & PROJECTS --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Big Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <i data-feather="layers" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded text-gray-500">All Time</span>
                    </div>
                    <h3 class="text-3xl font-bold">{{ $stats['projects_total'] }}</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase">Total Projects</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                            <i data-feather="activity" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold bg-green-100 text-green-600 px-2 py-1 rounded">Live</span>
                    </div>
                    <h3 class="text-3xl font-bold">{{ $stats['projects_active'] }}</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase">Active Projects</p>
                </div>
            </div>

            {{-- Recent Projects Table --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Recent Projects</h3>
                    <a href="{{ route('admin.projects.index') }}"
                        class="text-xs font-bold text-gray-500 hover:text-black">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Project Title</th>
                                <th class="px-6 py-4">Service</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recentProjects as $task)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-bold text-gray-900 block truncate max-w-[200px]">{{ $task->title }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 font-mono">#{{ substr($task->id, 0, 8) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">
                                            {{ $task->service->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColor = match ($task->status) {
                                                'queue' => 'bg-yellow-100 text-yellow-700',
                                                'active', 'in_progress' => 'bg-blue-100 text-blue-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 rounded-md text-[10px] font-bold uppercase {{ $statusColor }}">
                                            {{ str_replace('_', ' ', $task->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.projects.show', $task->id) }}"
                                            class="text-xs font-bold text-gray-500 hover:text-black underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">
                                        Client belum memiliki project.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
