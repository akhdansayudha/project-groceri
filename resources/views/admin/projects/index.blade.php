@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Project Management</h1>
        <p class="text-gray-500 text-sm">Pantau request masuk, progress pengerjaan, dan deadline tim.</p>
    </div>

    {{-- ALERT SUCCESS/ERROR --}}
    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-2 fade-in">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 flex items-center gap-2 fade-in">
            <i data-feather="alert-circle" class="w-4 h-4"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- TAB NAVIGATION (FILTER LENGKAP) --}}
    <div class="flex items-center gap-2 mb-6 border-b border-gray-200 overflow-x-auto pb-1 custom-scrollbar">
        {{-- ALL --}}
        <a href="{{ route('admin.projects.index') }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ !request('status') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            All <span class="ml-1 bg-gray-100 text-gray-600 px-1.5 rounded-md text-[10px]">{{ $counts['all'] }}</span>
        </a>

        {{-- QUEUE --}}
        <a href="{{ route('admin.projects.index', ['status' => 'queue']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'queue' ? 'border-yellow-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Queue <span
                class="ml-1 bg-yellow-100 text-yellow-700 px-1.5 rounded-md text-[10px]">{{ $counts['queue'] }}</span>
        </a>

        {{-- ACTIVE --}}
        <a href="{{ route('admin.projects.index', ['status' => 'active']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'active' ? 'border-blue-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Active <span
                class="ml-1 bg-blue-100 text-blue-700 px-1.5 rounded-md text-[10px]">{{ $counts['active'] }}</span>
        </a>

        {{-- IN PROGRESS --}}
        <a href="{{ route('admin.projects.index', ['status' => 'in_progress']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'in_progress' ? 'border-indigo-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            In Progress <span
                class="ml-1 bg-indigo-100 text-indigo-700 px-1.5 rounded-md text-[10px]">{{ $counts['in_progress'] }}</span>
        </a>

        {{-- REVIEW --}}
        <a href="{{ route('admin.projects.index', ['status' => 'review']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'review' ? 'border-purple-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Review <span
                class="ml-1 bg-purple-100 text-purple-700 px-1.5 rounded-md text-[10px]">{{ $counts['review'] }}</span>
        </a>

        {{-- REVISION --}}
        <a href="{{ route('admin.projects.index', ['status' => 'revision']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'revision' ? 'border-red-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
            Revision <span
                class="ml-1 bg-purple-100 text-red-700 px-1.5 rounded-md text-[10px]">{{ $counts['revision'] }}</span>
        </a>

        {{-- COMPLETED --}}
        <a href="{{ route('admin.projects.index', ['status' => 'completed']) }}"
            class="px-4 py-2 text-sm font-bold border-b-2 whitespace-nowrap transition-colors {{ request('status') == 'completed' ? 'border-green-500 text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
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
                        <th class="px-6 py-4">Workspace</th>
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

                            {{-- Client Info --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden border border-gray-100">
                                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-xs">{{ $task->user->full_name }}</p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ $task->user->wallet->tier->name ?? 'Starter' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Workspace --}}
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

                            {{-- Deadline (Dengan Logic Warna Merah < 48 Jam) --}}
                            <td class="px-6 py-4">
                                @php
                                    $isCompleted = $task->status == 'completed';
                                    // Hitung sisa jam
                                    $hoursRemaining = $task->deadline
                                        ? now()->diffInHours($task->deadline, false)
                                        : 999;
                                    // Urgent jika sisa waktu < 48 jam dan > 0 jam (belum lewat), serta belum selesai
                                    $isUrgent = !$isCompleted && $hoursRemaining < 48 && $hoursRemaining > 0;
                                    // Overdue jika deadline lewat dan belum selesai
                                    $isOverdue = !$isCompleted && $hoursRemaining < 0;
                                @endphp

                                <div
                                    class="flex items-center gap-2 text-xs font-medium 
                                    {{ $isUrgent ? 'text-red-600 bg-red-50 px-2 py-1 rounded-md border border-red-100' : '' }}
                                    {{ $isOverdue ? 'text-red-800 bg-red-100 px-2 py-1 rounded-md border border-red-200' : '' }}
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
                                        'queue' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
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

                            {{-- Assigned Staff (Profile + Nama) --}}
                            <td class="px-6 py-4">
                                @if ($task->assignee)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 overflow-hidden border border-gray-100">
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
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 bg-gray-50 rounded-full border border-gray-100">
                                        <i data-feather="inbox" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Tidak ada project ditemukan.</p>
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
