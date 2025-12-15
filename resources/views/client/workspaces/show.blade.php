@extends('client.layouts.app')

@section('content')
    {{-- HEADER & STATS (Tidak Berubah) --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.workspaces.index') }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $workspace->name }}</h1>
                <p class="text-gray-500 text-sm">{{ $workspace->description ?? 'Workspace Overview' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- DELETE BUTTON (Trigger Modal) --}}
            <button onclick="openDeleteModal()"
                class="group w-10 h-10 flex items-center justify-center rounded-xl border border-red-100 bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all"
                title="Delete Workspace">
                <i data-feather="trash-2" class="w-4 h-4"></i>
            </button>

            <a href="{{ route('client.requests.create', ['workspace_id' => $workspace->id]) }}"
                class="bg-black text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
                <i data-feather="plus-circle" class="w-4 h-4"></i>
                <span>New Request</span>
            </a>
        </div>
    </div>

    {{-- STATS GRID (Tidak Berubah) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 fade-in">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-400 font-bold uppercase">Total Projects</p>
            <h3 class="text-2xl font-bold mt-1">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-400 font-bold uppercase">Queue</p>
            <h3 class="text-2xl font-bold mt-1 text-yellow-600">{{ $stats['queue'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-400 font-bold uppercase">In Progress</p>
            <h3 class="text-2xl font-bold mt-1 text-blue-600">{{ $stats['active'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-400 font-bold uppercase">Completed</p>
            <h3 class="text-2xl font-bold mt-1 text-green-600">{{ $stats['completed'] }}</h3>
        </div>
    </div>

    {{-- PROJECT LIST TABLE (UPDATED) --}}
    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold">Project List</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        {{-- Kolom Workspace DIHAPUS karena ini sudah di detail workspace --}}
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- Title & ID --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 block truncate max-w-[200px]"
                                    title="{{ $task->title }}">
                                    {{ $task->title }}
                                </span>
                                <span class="text-[10px] text-gray-400">ID: #{{ substr($task->id, 0, 8) }}</span>
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

                                    {{-- Tombol Detail (Sama dengan Requests Index) --}}
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                        <i data-feather="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <p class="text-gray-900 font-medium">No projects in this workspace yet</p>
                                    <a href="{{ route('client.requests.create', ['workspace_id' => $workspace->id]) }}"
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

        {{-- Pagination Check (Jika tasks di-paginate dari controller) --}}
        @if ($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- DELETE WORKSPACE MODAL (Tidak Berubah) --}}
    <div id="deleteModal"
        class="fixed inset-0 z-[99] flex items-center justify-center bg-black/50 backdrop-blur-sm hidden fade-in p-4">
        <div class="bg-white p-8 rounded-3xl shadow-2xl max-w-md w-full relative transform transition-all scale-100">

            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 text-red-600">
                <i data-feather="alert-triangle" class="w-6 h-6"></i>
            </div>

            <h3 class="text-xl font-bold mb-2 text-gray-900">Delete Workspace?</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">
                Anda akan menghapus workspace <strong>"{{ $workspace->name }}"</strong>.
                Tindakan ini juga akan menghapus <strong class="text-red-600">{{ $stats['total'] }} project</strong> yang
                ada di dalamnya secara permanen.
            </p>

            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 py-3 border border-gray-200 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                    Cancel
                </button>

                <form action="{{ route('client.workspaces.destroy', $workspace->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-colors">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
