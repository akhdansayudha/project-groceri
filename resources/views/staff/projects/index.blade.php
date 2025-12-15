@extends('staff.layouts.app')

@section('content')
    {{-- HEADER --}}
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Active Projects</h1>
        <p class="text-gray-500 mt-1">Track progress, manage deliverables, and meet deadlines.</p>
    </div>

    {{-- CONTROL BAR (FILTER + SEARCH + SORT) --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 fade-in">

        {{-- 1. STATUS TABS (Scrollable on mobile) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 no-scrollbar">
            @php
                function buildUrl($key, $currentParams)
                {
                    $params = $currentParams;
                    $params['status'] = $key; // Override status
                    return route('staff.projects.index', $params);
                }

                function navClass($key, $currentStatus)
                {
                    $base =
                        'flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all border ';
                    if (($key == 'all' && !$currentStatus) || $key == $currentStatus) {
                        return $base . 'bg-black text-white border-black shadow-lg shadow-black/20';
                    }
                    return $base . 'bg-white text-gray-500 border-gray-200 hover:border-gray-300 hover:text-black';
                }

                // Ambil semua parameter URL saat ini (search, sort) agar tidak hilang saat ganti tab
                $params = request()->all();
            @endphp

            <a href="{{ buildUrl('all', $params) }}" class="{{ navClass('all', $status) }}">
                All <span class="opacity-60 ml-1">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ buildUrl('active', $params) }}" class="{{ navClass('active', $status) }}">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                To Start <span class="opacity-60 ml-1">{{ $counts['active'] }}</span>
            </a>
            <a href="{{ buildUrl('in_progress', $params) }}" class="{{ navClass('in_progress', $status) }}">
                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                Progress <span class="opacity-60 ml-1">{{ $counts['in_progress'] }}</span>
            </a>
            <a href="{{ buildUrl('revision', $params) }}" class="{{ navClass('revision', $status) }}">
                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                Revision <span class="opacity-60 ml-1">{{ $counts['revision'] }}</span>
            </a>
            <a href="{{ buildUrl('review', $params) }}" class="{{ navClass('review', $status) }}">
                <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                Review <span class="opacity-60 ml-1">{{ $counts['review'] }}</span>
            </a>
        </div>

        {{-- 2. SEARCH & SORT FORM --}}
        <form action="{{ route('staff.projects.index') }}" method="GET"
            class="flex flex-col sm:flex-row gap-3 lg:w-auto w-full">
            {{-- Hidden input untuk mempertahankan status saat search --}}
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            {{-- SEARCH INPUT --}}
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search ID or Project Name..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-black focus:border-transparent transition-all shadow-sm">
                <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            </div>

            {{-- SORT DROPDOWN --}}
            <div class="relative w-full sm:w-48">
                <select name="sort" onchange="this.form.submit()"
                    class="w-full pl-10 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-black focus:border-transparent transition-all shadow-sm appearance-none cursor-pointer">
                    <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest Assigned</option>
                    <option value="deadline" {{ $sort == 'deadline' ? 'selected' : '' }}>Deadline (Earliest)</option>
                </select>
                <i data-feather="filter" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <i data-feather="chevron-down"
                    class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
        </form>
    </div>

    {{-- PROJECTS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 fade-in pb-10">

        @forelse($tasks as $task)
            <div
                class="group bg-white rounded-3xl p-1 border border-gray-200 shadow-sm hover:shadow-xl hover:border-gray-300 hover:-translate-y-1 transition-all duration-300">
                <div class="bg-white rounded-[20px] p-5 h-full flex flex-col justify-between">

                    {{-- TOP SECTION: ID & Service Badge --}}
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-col">
                            <span
                                class="text-[10px] font-mono font-bold text-gray-400 tracking-wider">#{{ substr($task->id, 0, 8) }}</span>
                            {{-- DEADLINE --}}
                            @if ($task->deadline)
                                @php
                                    $daysLeft = now()->diffInDays($task->deadline, false);
                                    $deadlineClass = $daysLeft < 2 ? 'text-red-500' : 'text-gray-500';
                                    $iconClass = $daysLeft < 2 ? 'text-red-500 animate-pulse' : 'text-gray-400';
                                @endphp
                                <div class="flex items-center gap-1.5 mt-1">
                                    <i data-feather="clock" class="w-3 h-3 {{ $iconClass }}"></i>
                                    <span class="text-[10px] font-bold {{ $deadlineClass }}">
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- SERVICE BADGE (Lebih Menarik & Besar) --}}
                        <span
                            class="px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-black text-white shadow-md shadow-gray-200 group-hover:bg-blue-600 transition-colors">
                            {{ $task->service->name }}
                        </span>
                    </div>

                    {{-- MIDDLE SECTION: Title & Desc --}}
                    <div class="mb-6">
                        <h3
                            class="font-bold text-lg text-gray-900 leading-tight mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <a href="{{ route('staff.projects.show', $task->id) }}" class="focus:outline-none">
                                {{ $task->title }}
                            </a>
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                            {{ Str::limit($task->description, 100) }}
                        </p>
                    </div>

                    {{-- BOTTOM SECTION: Client & Action --}}
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">

                        {{-- Client Info (Only Avatar + Name) --}}
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-100 p-0.5 ring-1 ring-gray-100 group-hover:ring-blue-100 transition-all">
                                <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name . '&background=random' }}"
                                    class="w-full h-full object-cover rounded-full" alt="Client">
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900 leading-none">
                                    {{ Str::limit($task->user->full_name, 15) }}
                                </p>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5">Client</p>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('staff.projects.show', $task->id) }}"
                            class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 border border-gray-100 group-hover:bg-black group-hover:text-white group-hover:border-black transition-all shadow-sm">
                            <i data-feather="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>

        @empty
            {{-- EMPTY STATE --}}
            <div
                class="col-span-full flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50">
                <div
                    class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-gray-100">
                    <i data-feather="search" class="w-6 h-6 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No projects found</h3>
                <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">
                    We couldn't find any projects matching your current filters or search keywords.
                </p>
                <a href="{{ route('staff.projects.index') }}"
                    class="mt-4 px-6 py-2 bg-black text-white text-xs font-bold rounded-full hover:bg-gray-800 transition-colors">
                    Clear Filters
                </a>
            </div>
        @endforelse

    </div>
@endsection
