@extends('admin.layouts.app')

@section('content')
    {{-- HEADER --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.workspaces.index') }}"
                class="w-12 h-12 rounded-2xl border border-gray-200 flex items-center justify-center hover:bg-black hover:text-white transition-all shadow-sm">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $workspace->name }}</h1>
                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-lg text-[10px] font-mono font-bold uppercase">
                        WORKSPACE
                    </span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                    <span
                        class="flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1 rounded-full text-xs font-medium">
                        <i data-feather="user" class="w-3 h-3"></i> {{ $workspace->user->full_name }}
                    </span>
                    <span>&bull;</span>
                    <span class="text-xs">Created {{ $workspace->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS GRID (Redesigned Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 fade-in">
        {{-- Total Projects --}}
        <div
            class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Total Projects</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-600 group-hover:bg-black group-hover:text-white transition-colors">
                <i data-feather="layers" class="w-5 h-5"></i>
            </div>
        </div>

        {{-- Active Projects --}}
        <div
            class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest mb-1">Active Now</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ $stats['active'] }}</h3>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i data-feather="activity" class="w-5 h-5"></i>
            </div>
        </div>

        {{-- Queue --}}
        <div
            class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-[10px] text-yellow-500 font-bold uppercase tracking-widest mb-1">Queue / Pending</p>
                <h3 class="text-3xl font-bold text-yellow-600">{{ $stats['queue'] }}</h3>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-yellow-50 flex items-center justify-center text-yellow-600 group-hover:bg-yellow-500 group-hover:text-white transition-colors">
                <i data-feather="clock" class="w-5 h-5"></i>
            </div>
        </div>

        {{-- Storage --}}
        <div
            class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-[10px] text-purple-400 font-bold uppercase tracking-widest mb-1">Storage Used</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ $stats['storage'] }}</h3>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                <i data-feather="hard-drive" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    {{-- PROJECT LIST TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
            <div>
                <h3 class="font-bold text-lg text-gray-900">Projects in Workspace</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar semua project yang terhubung dengan workspace ini.</p>
            </div>
            <span class="text-xs font-bold bg-gray-100 text-gray-600 px-3 py-1 rounded-full">{{ $tasks->total() }}
                Projects</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Project Details</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Assigned To</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- Project Details --}}
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 mb-0.5 truncate max-w-[200px]"
                                    title="{{ $task->title }}">
                                    {{ Str::limit($task->title, 25) }}
                                </p>
                                <p class="text-[10px] text-gray-400 font-mono">ID: #{{ substr($task->id, 0, 8) }}</p>
                            </td>

                            {{-- Client --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden border border-gray-100">
                                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-xs">{{ $task->user->full_name }}</p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ $task->user->wallet->tier->name ?? 'Starter' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Service --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-700 uppercase tracking-wide">
                                    {{ $task->service->name }}
                                </span>
                            </td>

                            {{-- Deadline (Logic Warna Merah) --}}
                            <td class="px-6 py-4">
                                @php
                                    $isCompleted = $task->status == 'completed';
                                    $hoursRemaining = $task->deadline
                                        ? now()->diffInHours($task->deadline, false)
                                        : 999;

                                    // Urgent: < 48 jam & belum selesai
                                    $isUrgent = !$isCompleted && $hoursRemaining < 48 && $hoursRemaining > 0;
                                    // Overdue: Lewat deadline & belum selesai
                                    $isOverdue = !$isCompleted && $hoursRemaining < 0;
                                @endphp

                                <div
                                    class="flex items-center gap-2 text-xs font-medium 
                                    {{ $isUrgent ? 'text-red-600 bg-red-50 px-2 py-1 rounded-md border border-red-100 w-fit' : '' }}
                                    {{ $isOverdue ? 'text-red-800 bg-red-100 px-2 py-1 rounded-md border border-red-200 w-fit' : '' }}
                                    {{ !$isUrgent && !$isOverdue ? 'text-gray-600' : '' }}">

                                    @if ($isUrgent || $isOverdue)
                                        <i data-feather="alert-circle" class="w-3 h-3"></i>
                                    @else
                                        <i data-feather="calendar" class="w-3 h-3 text-gray-400"></i>
                                    @endif

                                    {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Status Badge (Warna Warni) --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = match ($task->status) {
                                        'queue' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'active' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'in_progress' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'review' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'revision' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'completed' => 'bg-green-50 text-green-700 border-green-200',
                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase border tracking-wider {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>

                            {{-- Assigned (Profile + Nama) --}}
                            <td class="px-6 py-4">
                                @if ($task->assignee)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden border border-gray-100 shrink-0">
                                            <img title="{{ $task->assignee->full_name }}"
                                                src="{{ $task->assignee->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->assignee->full_name) }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <span class="text-xs font-medium text-gray-700 truncate max-w-[100px]">
                                            {{ Str::limit($task->assignee->full_name, 20) }}
                                        </span>
                                    </div>
                                @else
                                    <span
                                        class="flex items-center gap-1 text-[10px] text-gray-400 italic bg-gray-50 px-2 py-1 rounded border border-gray-100 w-fit">
                                        <i data-feather="user-x" class="w-3 h-3"></i> Unassigned
                                    </span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.projects.show', $task->id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:bg-black hover:text-white hover:border-black transition-all shadow-sm">
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 bg-gray-50 rounded-full border border-gray-100">
                                        <i data-feather="inbox" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Workspace ini masih kosong.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($tasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@endsection
