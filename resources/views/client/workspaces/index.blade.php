@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">My Workspaces</h1>
            <p class="text-gray-500">Select a workspace to manage projects or create a new request.</p>
        </div>

        @if ($workspaces->count() < $maxWorkspaces)
            <button onclick="document.getElementById('createWsForm').classList.toggle('hidden')"
                class="bg-black text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i> Create Workspace
            </button>
        @endif
    </div>

    <div id="createWsForm" class="hidden mb-8 bg-white p-6 rounded-3xl border border-gray-200 shadow-sm fade-in">
        <form action="{{ route('client.workspaces.store') }}" method="POST"
            class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="w-full">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Name</label>
                <input type="text" name="name" required placeholder="e.g. Brand A"
                    class="w-full border-b border-gray-200 py-2 focus:border-black outline-none">
            </div>
            <div class="w-full">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Description</label>
                <input type="text" name="description" placeholder="Optional"
                    class="w-full border-b border-gray-200 py-2 focus:border-black outline-none">
            </div>
            <button type="submit" class="bg-black text-white px-6 py-2.5 rounded-lg font-bold text-sm">Save</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 fade-in">
        @forelse($workspaces as $ws)
            <a href="{{ route('client.workspaces.show', $ws->id) }}"
                class="group bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md hover:border-black transition-all relative overflow-hidden">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-black group-hover:text-white transition-colors">
                        <i data-feather="folder" class="w-6 h-6"></i>
                    </div>
                    <div
                        class="bg-gray-100 px-2 py-1 rounded text-[10px] font-bold text-gray-500 group-hover:bg-gray-800 group-hover:text-white">
                        {{ $ws->tasks_count }} Projects
                    </div>
                </div>

                <h3 class="font-bold text-lg mb-1">{{ $ws->name }}</h3>
                <p class="text-xs text-gray-400 line-clamp-2">{{ $ws->description ?? 'No description provided.' }}</p>

                <div
                    class="mt-4 pt-4 border-t border-gray-100 flex items-center text-xs font-bold text-gray-400 group-hover:text-black">
                    <span>Open Folder</span>
                    <i data-feather="arrow-right" class="w-3 h-3 ml-2"></i>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12 border-2 border-dashed border-gray-200 rounded-3xl">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="folder-plus" class="w-8 h-8 text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">No workspaces found.</p>
                <p class="text-xs text-gray-400 mt-1">Create your first workspace to start managing projects.</p>
            </div>
        @endforelse
    </div>
@endsection
