@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">My Requests</h1>
            <p class="text-gray-500">Track and manage all your project requests in one place.</p>
        </div>

        <a href="{{ route('client.requests.create') }}"
            class="bg-black text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
            <i data-feather="plus-circle" class="w-4 h-4"></i>
            <span>New Request</span>
        </a>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-8 fade-in">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">All Time</p>
                <h3 class="text-2xl font-bold mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400">
                <i data-feather="layers" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">Active Now</p>
                <h3 class="text-2xl font-bold mt-1 text-blue-600">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                <i data-feather="activity" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase">Completed</p>
                <h3 class="text-2xl font-bold mt-1 text-green-600">{{ $stats['completed'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                <i data-feather="check-circle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <div class="mb-6 fade-in bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <form method="GET" action="{{ route('client.requests.index') }}" class="flex flex-col md:flex-row gap-4">

            <div class="flex-1 relative group">
                <i data-feather="search"
                    class="w-4 h-4 absolute left-3 top-3.5 text-gray-400 group-focus-within:text-black transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by project title..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 pl-10 pr-4 text-sm font-medium focus:outline-none focus:border-black focus:bg-white transition-all">
            </div>

            <div class="w-full md:w-48">
                <select name="workspace_id" onchange="this.form.submit()"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium focus:outline-none focus:border-black cursor-pointer">
                    <option value="all">All Workspaces</option>
                    @foreach ($workspaces as $ws)
                        <option value="{{ $ws->id }}" {{ request('workspace_id') == $ws->id ? 'selected' : '' }}>
                            {{ $ws->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-40">
                <select name="status" onchange="this.form.submit()"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium focus:outline-none focus:border-black cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="queue" {{ request('status') == 'queue' ? 'selected' : '' }}>Queue</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            @if (request()->has('search') || request()->has('workspace_id') || request()->has('status'))
                <a href="{{ route('client.requests.index') }}"
                    class="px-4 py-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black transition-colors flex items-center justify-center">
                    <i data-feather="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Workspace</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- Title --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 block truncate max-w-[200px]"
                                    title="{{ $task->title }}">
                                    {{ $task->title }}
                                </span>
                                <span class="text-[10px] text-gray-400">ID: #{{ substr($task->id, 0, 8) }}</span>
                            </td>

                            {{-- Workspace (Dengan Icon Folder) --}}
                            <td class="px-6 py-4">
                                @if ($task->workspace)
                                    <a href="{{ route('client.workspaces.show', $task->workspace_id) }}"
                                        class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-gray-100 bg-gray-50 text-xs font-bold text-gray-600 hover:bg-black hover:text-white hover:border-black transition-all">
                                        <i data-feather="folder" class="w-3 h-3"></i>
                                        {{ $task->workspace->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-xs">Unassigned</span>
                                @endif
                            </td>

                            {{-- Service --}}
                            <td class="px-6 py-4 text-gray-500 font-medium">
                                {{ $task->service->name ?? '-' }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'queue' => 'bg-gray-100 text-gray-600',
                                        'active' => 'bg-blue-100 text-blue-700',
                                        'in_progress' => 'bg-indigo-100 text-indigo-700',
                                        'review' => 'bg-yellow-100 text-yellow-800',
                                        'revision' => 'bg-orange-100 text-orange-800',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>

                            {{-- Deadline --}}
                            <td class="px-6 py-4 text-gray-500">
                                @if ($task->deadline)
                                    <div class="flex items-center gap-2">
                                        <i data-feather="calendar" class="w-3 h-3"></i>
                                        {{ $task->deadline->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">

                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('client.requests.show', $task->id) }}"
                                        class="text-xs font-bold text-gray-500 hover:text-black transition-colors border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                                        View
                                    </a>

                                    {{-- Tombol Hapus (Hanya jika Queue) --}}
                                    @if ($task->status == 'queue')
                                        <form action="{{ route('client.requests.destroy', $task->id) }}" method="POST"
                                            onsubmit="return confirm('Batalkan project ini? Token akan dikembalikan.');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-red-50"
                                                title="Cancel & Refund">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                        <i data-feather="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-gray-900 font-medium">No requests found</p>
                                    <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">
                                        Try adjusting your search or filters, or create a new request to get started.
                                    </p>
                                    <a href="{{ route('client.requests.create') }}"
                                        class="mt-4 text-xs font-bold text-black border-b border-black pb-0.5 hover:opacity-70">
                                        Create New Request
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
