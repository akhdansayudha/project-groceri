@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Live Monitoring</h1>
            <p class="text-gray-500 text-sm">Monitor seluruh workspace client dan penggunaan resource (Storage Estimates).
            </p>
        </div>

        {{-- SEARCH BAR --}}
        <form action="{{ route('admin.workspaces.index') }}" method="GET" class="w-full md:w-auto">
            <div class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search workspace..."
                    class="w-full md:w-80 bg-white border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-sm focus:outline-none focus:border-black transition-all shadow-sm">
                <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>

                @if (request('search'))
                    <a href="{{ route('admin.workspaces.index') }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                        <i data-feather="x" class="w-3 h-3"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- GLOBAL SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-black text-white rounded-2xl flex items-center justify-center">
                <i data-feather="grid" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Workspaces</p>
                <h3 class="text-2xl font-bold">{{ $totalWorkspaces }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                <i data-feather="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Projects</p>
                <h3 class="text-2xl font-bold">{{ $totalProjectsGlobal }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center">
                <i data-feather="hard-drive" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Attachments Size</p>
                <h3 class="text-2xl font-bold">{{ $totalStorage }} <span class="text-xs text-gray-400 font-normal">/
                        Est.</span></h3>
            </div>
        </div>
    </div>

    {{-- GRID WORKSPACES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 fade-in">

        @forelse($workspaces as $workspace)
            <div
                class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all group relative overflow-hidden">

                {{-- Folder Icon & Live Indicator --}}
                <div class="flex justify-between items-start mb-4">
                    <div
                        class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600 group-hover:bg-yellow-100 group-hover:scale-110 transition-all duration-300">
                        <i data-feather="folder" class="w-6 h-6 fill-current"></i>
                    </div>

                    @if ($workspace->active_projects > 0)
                        <span
                            class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded-md border border-green-100 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Live
                        </span>
                    @endif
                </div>

                {{-- Workspace Info --}}
                <h3 class="font-bold text-lg text-gray-900 mb-1 truncate" title="{{ $workspace->name }}">
                    {{ $workspace->name }}
                </h3>
                <p class="text-xs text-gray-400 mb-6">Created {{ $workspace->created_at->format('d M Y') }}</p>

                {{-- Stats Row (Updated with calculated storage) --}}
                <div
                    class="flex items-center justify-between text-xs font-medium text-gray-500 mb-6 border-y border-gray-50 py-3">
                    <div class="flex items-center gap-1.5" title="Total Projects">
                        <i data-feather="layers" class="w-3.5 h-3.5"></i> {{ $workspace->total_projects }} Projects
                    </div>
                    <div class="flex items-center gap-1.5" title="Active Projects">
                        <i data-feather="activity"
                            class="w-3.5 h-3.5 {{ $workspace->active_projects > 0 ? 'text-green-500' : '' }}"></i>
                        {{ $workspace->active_projects }} Actives
                    </div>
                    <div class="flex items-center gap-1.5" title="Storage Used (Estimated)">
                        <i data-feather="hard-drive" class="w-3.5 h-3.5"></i> {{ $workspace->storage_est }}
                    </div>
                </div>

                {{-- Owner Info --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="{{ $workspace->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $workspace->user->full_name }}"
                            class="w-6 h-6 rounded-full border border-gray-100">
                        <span
                            class="text-xs font-bold text-gray-700 truncate max-w-[100px]">{{ $workspace->user->full_name }}</span>
                    </div>

                    <a href="{{ route('admin.workspaces.show', $workspace->id) }}"
                        class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center hover:bg-black hover:text-white hover:border-black transition-colors">
                        <i data-feather="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>

                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 to-yellow-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left">
                </div>
            </div>
        @empty
            <div
                class="col-span-full py-16 flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200 rounded-3xl">
                <div class="p-4 bg-gray-50 rounded-full mb-3">
                    <i data-feather="folder-minus" class="w-8 h-8"></i>
                </div>
                <p class="font-bold text-lg text-gray-600">No workspaces found.</p>
                @if (request('search'))
                    <p class="text-sm mt-1">Try searching with a different keyword.</p>
                    <a href="{{ route('admin.workspaces.index') }}"
                        class="mt-4 text-xs font-bold text-black border-b border-black pb-0.5">Clear Search</a>
                @else
                    <p class="text-sm mt-1">Client belum membuat workspace apapun.</p>
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $workspaces->links() }}
    </div>
@endsection
