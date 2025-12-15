@extends('admin.layouts.app')

@section('content')
    {{-- HEADER --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.workspaces.index') }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $workspace->name }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                    <span class="flex items-center gap-1">
                        <i data-feather="user" class="w-3 h-3"></i> {{ $workspace->user->full_name }}
                    </span>
                    <span>•</span>
                    <span>Created {{ $workspace->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 fade-in">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Projects</p>
            <h3 class="text-2xl font-bold mt-1 text-gray-900">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Active Now</p>
            <h3 class="text-2xl font-bold mt-1 text-blue-600">{{ $stats['active'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Queue / Pending</p>
            <h3 class="text-2xl font-bold mt-1 text-yellow-600">{{ $stats['queue'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Attachment Size</p>
            <h3 class="text-2xl font-bold mt-1 text-purple-600">{{ $stats['storage'] }} <span
                    class="text-xs text-gray-300 font-normal">Est.</span></h3>
        </div>
    </div>

    {{-- PROJECT LIST TABLE (UPDATED) --}}
    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-900">Projects in Workspace</h3>
            <span class="text-xs text-gray-400">{{ $tasks->total() }} records found</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Project Details</th>
                        <th class="px-6 py-4">Client</th>
                        {{-- Kolom Workspace Dihapus --}}
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Assigned</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors group">

                            {{-- Project Details --}}
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

                            {{-- Service --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-700 uppercase tracking-wide">
                                    {{ $task->service->name }}
                                </span>
                            </td>

                            {{-- Deadline --}}
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

                            {{-- Assigned --}}
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
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-3 bg-gray-50 rounded-full">
                                        <i data-feather="inbox" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="font-medium text-gray-500">Workspace ini masih kosong.</p>
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
