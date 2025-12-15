@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Project Management</h1>
        <p class="text-gray-500 text-sm">Pantau request masuk dan progress pengerjaan tim.</p>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex items-center gap-1 mb-6 border-b border-gray-200 overflow-x-auto">
        <a href="{{ route('admin.projects.index') }}"
            class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ !request('status') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            All Projects <span
                class="ml-1 bg-gray-100 text-gray-600 px-1.5 rounded-md text-[10px]">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.projects.index', ['status' => 'queue']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ request('status') == 'queue' ? 'border-yellow-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Queue / Pending <span
                class="ml-1 bg-yellow-100 text-yellow-700 px-1.5 rounded-md text-[10px]">{{ $counts['queue'] }}</span>
        </a>
        <a href="{{ route('admin.projects.index', ['status' => 'active']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ request('status') == 'active' ? 'border-blue-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Active <span class="ml-1 bg-blue-100 text-blue-700 px-1.5 rounded-md text-[10px]">{{ $counts['active'] }}</span>
        </a>
        <a href="{{ route('admin.projects.index', ['status' => 'completed']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ request('status') == 'completed' ? 'border-green-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Completed <span
                class="ml-1 bg-green-100 text-green-700 px-1.5 rounded-md text-[10px]">{{ $counts['completed'] }}</span>
        </a>
    </div>

    {{-- PROJECT TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Project Details</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Workspace</th> {{-- KOLOM BARU --}}
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Deadline</th> {{-- KOLOM BARU --}}
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Assigned</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- Project Details (Title + ID) --}}
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 mb-0.5">{{ Str::limit($task->title, 25) }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">ID: #{{ substr($task->id, 0, 8) }}</p>
                            </td>

                            {{-- Client --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
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

                            {{-- Workspace (BARU) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i data-feather="folder" class="w-3 h-3 text-gray-400"></i>
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ $task->workspace->name ?? 'Default' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Service --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-700 uppercase tracking-wide">
                                    {{ $task->service->name }}
                                </span>
                            </td>

                            {{-- Deadline (BARU) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <i data-feather="calendar" class="w-3 h-3 text-gray-400"></i>
                                    {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match ($task->status) {
                                        'queue' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'active', 'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'completed' => 'bg-green-100 text-green-700 border-green-200',
                                        'revision' => 'bg-red-100 text-red-700 border-red-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $statusColor }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>

                            {{-- Assigned Staff --}}
                            <td class="px-6 py-4">
                                @if ($task->assignee)
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <img title="{{ $task->assignee->full_name }}"
                                            src="{{ $task->assignee->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->assignee->full_name }}"
                                            class="inline-block h-8 w-8 rounded-full ring-2 ring-white">
                                    </div>
                                @else
                                    <span
                                        class="text-[10px] text-gray-400 italic bg-gray-50 px-2 py-1 rounded border border-gray-100">Unassigned</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.projects.show', $task->id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 hover:bg-black hover:text-white hover:border-black transition-all">
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-3 bg-gray-50 rounded-full">
                                        <i data-feather="inbox" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p>Tidak ada project ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($tasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@endsection
