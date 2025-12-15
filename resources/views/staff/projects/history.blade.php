@extends('staff.layouts.app')

@section('content')
    <div class="fade-in">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Project History</h1>
            <p class="text-gray-500 mt-1">Archive of all your completed works and performance stats.</p>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            {{-- Card 1: Total Earned --}}
            <div class="bg-black text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <div class="p-3 bg-white/20 rounded-xl w-fit mb-4">
                        <i data-feather="zap" class="w-6 h-6 text-yellow-400 fill-yellow-400"></i>
                    </div>
                    <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">Lifetime Earnings</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalEarned) }} <span
                            class="text-sm font-normal text-gray-400">Token</span></h3>
                </div>
                {{-- Decoration --}}
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-gray-800 rounded-full opacity-50 blur-2xl"></div>
            </div>

            {{-- Card 2: Completed Projects --}}
            <div class="bg-white border border-gray-200 p-6 rounded-3xl shadow-sm">
                <div class="p-3 bg-green-50 rounded-xl w-fit mb-4 border border-green-100">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">Projects Completed</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($totalCompleted) }}</h3>
            </div>

            {{-- Card 3: Top Service --}}
            <div class="bg-white border border-gray-200 p-6 rounded-3xl shadow-sm">
                <div class="p-3 bg-purple-50 rounded-xl w-fit mb-4 border border-purple-100">
                    <i data-feather="star" class="w-6 h-6 text-purple-600 fill-purple-200"></i>
                </div>
                <p class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-1">Top Expertise</p>
                <h3 class="text-xl font-bold text-gray-900 truncate" title="{{ $topServiceName }}">
                    {{ $topServiceName }}
                </h3>
            </div>
        </div>

        {{-- CONTENT CARD --}}
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">

            {{-- TOOLBAR (Search) --}}
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <form action="{{ route('staff.projects.history') }}" method="GET" class="relative w-full max-w-md">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search ID or Title..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-black focus:border-transparent transition-all shadow-sm">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-100 text-[10px] uppercase text-gray-400 font-bold tracking-wider">
                            <th class="px-6 py-4">ID</th> {{-- KOLOM ID DI KIRI --}}
                            <th class="px-6 py-4">Project Title & Service</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4 text-center">Completed Date</th>
                            <th class="px-6 py-4 text-center">Earnings</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($tasks as $task)
                            <tr class="hover:bg-gray-50/50 transition-colors group">

                                {{-- 1. ID PROJECT (Terpisah) --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
                                        #{{ substr($task->id, 0, 8) }}
                                    </span>
                                </td>

                                {{-- 2. Project Details --}}
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                            {{ Str::limit($task->title, 40) }}
                                        </p>
                                        <span
                                            class="text-[10px] font-bold text-white bg-black px-2 py-0.5 rounded-full uppercase">
                                            {{ $task->service->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 3. Client --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                                            class="w-6 h-6 rounded-full border border-gray-100">
                                        <span class="font-medium text-gray-700">{{ $task->user->full_name }}</span>
                                    </div>
                                </td>

                                {{-- 4. Completed Date --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-medium text-gray-500">
                                        {{ $task->completed_at ? $task->completed_at->format('d M Y') : '-' }}
                                    </span>
                                </td>

                                {{-- 5. Earnings --}}
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <i data-feather="zap" class="w-3 h-3 text-yellow-500 fill-yellow-500"></i>
                                        <span
                                            class="font-bold text-yellow-700">{{ number_format($task->toratix_locked) }}</span>
                                    </div>
                                </td>

                                {{-- 6. Action --}}
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('staff.projects.show', $task->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-400 hover:bg-black hover:text-white hover:border-black transition-all shadow-sm">
                                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                            <i data-feather="archive" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-900 font-bold text-sm">No history found</p>
                                        <p class="text-gray-500 text-xs mt-1">You haven't completed any projects yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($tasks->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $tasks->appends(['search' => $search])->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
