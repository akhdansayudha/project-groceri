@extends('admin.layouts.app')

@section('content')
    {{-- HEADER & NAVIGATION --}}
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.projects.index') }}"
                class="p-2.5 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Project #{{ substr($task->id, 0, 8) }}</h1>
                    <span
                        class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 text-[10px] font-mono font-bold uppercase tracking-wider">
                        {{ $task->service->name }}
                    </span>
                </div>
                <p class="text-gray-500 text-xs mt-1">Submitted on {{ $task->created_at->format('d F Y, H:i') }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button
                class="px-4 py-2 bg-white border border-gray-200 text-red-500 rounded-xl text-xs font-bold hover:bg-red-50 hover:border-red-200 transition-colors flex items-center gap-2">
                <i data-feather="trash-2" class="w-4 h-4"></i> Delete
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-2 fade-in">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- KOLOM KIRI: FULL DETAILS --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Project Specification (Utama & Additional) --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="file-text" class="w-5 h-5 text-gray-500"></i> Project Specification
                    </h3>
                </div>

                <div class="p-8 space-y-8">
                    {{-- Judul Project --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Project
                            Title</label>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-900 font-bold text-lg">
                            {{ $task->title ?? '-' }}
                        </div>
                    </div>

                    {{-- Grid Info Dasar --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Service
                                Category</label>
                            <input type="text" readonly value="{{ $task->service->name ?? '-' }}"
                                class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 text-sm focus:outline-none font-medium">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Target
                                Deadline</label>
                            <div class="relative">
                                <input type="text" readonly
                                    value="{{ $task->deadline ? $task->deadline->format('d M Y') : 'No Deadline' }}"
                                    class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 pl-11 text-sm focus:outline-none font-medium">
                                <i data-feather="calendar"
                                    class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Workspace</label>
                            <div class="relative">
                                <input type="text" readonly value="{{ $task->workspace->name ?? 'Default Workspace' }}"
                                    class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 pl-11 text-sm focus:outline-none font-medium">
                                <i data-feather="folder"
                                    class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Budget /
                                Locked</label>
                            <div class="relative">
                                {{-- PERBAIKAN: Menggunakan toratix_locked sesuai DB --}}
                                <input type="text" readonly value="{{ $task->toratix_locked ?? 0 }} Tokens"
                                    class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 pl-11 text-sm focus:outline-none font-bold">
                                <i data-feather="zap"
                                    class="w-4 h-4 text-yellow-500 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>
                    </div>

                    {{-- ADDITIONAL DETAILS (Dari JSON brief_data) --}}
                    @php
                        // Decode JSON data (bisa array atau object)
                        $brief = is_string($task->brief_data)
                            ? json_decode($task->brief_data, true)
                            : $task->brief_data;
                    @endphp

                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                            <i data-feather="sliders" class="w-3 h-3"></i> Additional Details
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">Brand Color</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm"
                                        style="background-color: {{ $brief['color'] ?? '#ffffff' }}"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $brief['color'] ?? '-' }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">Target
                                    Audience</label>
                                <span class="text-sm font-medium text-gray-700">{{ $brief['audience'] ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-[10px] text-gray-400 font-bold uppercase mb-1 block">Reference
                                    Link</label>
                                @if (!empty($brief['reference']))
                                    <a href="{{ $brief['reference'] }}" target="_blank"
                                        class="text-sm font-bold text-blue-600 hover:underline flex items-center gap-1">
                                        {{ Str::limit($brief['reference'], 50) }} <i data-feather="external-link"
                                            class="w-3 h-3"></i>
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Detailed
                            Brief / Description</label>
                        <div
                            class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-5 text-sm leading-relaxed min-h-[150px]">
                            @if ($task->description)
                                {!! nl2br(e($task->description)) !!}
                            @else
                                <span class="text-gray-400 italic">Client did not provide a description.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Attachments / Files --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="paperclip" class="w-5 h-5 text-gray-500"></i> Reference Files
                    </h3>
                </div>
                <div class="p-8">
                    {{-- Logic Deteksi File (Single Object Check) --}}
                    @php
                        // Decode jika string, atau pakai langsung jika sudah array
                        $file = is_string($task->attachments)
                            ? json_decode($task->attachments, true)
                            : $task->attachments;
                    @endphp

                    {{-- Pastikan data valid & memiliki key 'path' --}}
                    @if (isset($file) && !empty($file) && isset($file['path']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($file['path']) }}"
                                target="_blank"
                                class="group flex items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-black hover:shadow-md transition-all bg-white">

                                {{-- Ikon File Dinamis --}}
                                <div
                                    class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-black group-hover:text-white transition-colors border border-gray-200">
                                    @php
                                        $mime = $file['mime_type'] ?? '';
                                    @endphp
                                    @if (str_contains($mime, 'image'))
                                        <i data-feather="image" class="w-5 h-5"></i>
                                    @elseif(str_contains($mime, 'pdf'))
                                        <i data-feather="file-text" class="w-5 h-5"></i>
                                    @else
                                        <i data-feather="file" class="w-5 h-5"></i>
                                    @endif
                                </div>

                                {{-- Info File --}}
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:underline"
                                        title="{{ $file['original_name'] }}">
                                        {{ $file['original_name'] }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-[10px] font-mono text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                            {{ isset($file['size']) ? number_format($file['size'] / 1024, 1) . ' KB' : 'Unknown' }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 uppercase">
                                            {{ isset($file['uploaded_at']) ? \Carbon\Carbon::parse($file['uploaded_at'])->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                </div>

                                <i data-feather="external-link" class="w-4 h-4 text-gray-300 group-hover:text-black"></i>
                            </a>
                        </div>
                    @else
                        {{-- Tampilan Jika Kosong --}}
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                            <div
                                class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-300">
                                <i data-feather="file-minus" class="w-5 h-5"></i>
                            </div>
                            <p class="text-gray-400 text-sm">No files attached by client.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: ACTION & TIMELINE --}}
        <div class="space-y-6">

            {{-- 1. CLIENT CARD (Chat Room) --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm relative overflow-hidden group">
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="relative">
                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                            class="w-14 h-14 rounded-full border-2 border-white shadow-md object-cover">

                        {{-- REALTIME STATUS BADGE --}}
                        @if ($isUserOnline ?? false)
                            <div title="Client Online"
                                class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full animate-pulse shadow-sm">
                            </div>
                        @else
                            <div title="Client Offline"
                                class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-gray-400 border-2 border-white rounded-full shadow-sm">
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Project Owner</p>
                        <p class="font-bold text-gray-900 text-lg leading-tight">{{ $task->user->full_name }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span
                                class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase rounded">{{ $task->user->wallet->tier->name ?? 'Client' }}</span>

                            @if ($isUserOnline ?? false)
                                <span class="text-[10px] text-green-600 font-bold">• Online</span>
                            @else
                                <span class="text-[10px] text-gray-400 font-bold">• Offline</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CHAT BUTTON LOGIC --}}
                @if ($task->status !== 'queue')
                    {{-- JIKA SUDAH ASSIGNED / ACTIVE: TOMBOL CHAT MUNCUL --}}
                    <a href="{{ route('admin.projects.chat', $task->id) }}"
                        class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-black/20 relative z-10">
                        <i data-feather="message-circle" class="w-4 h-4"></i>
                        Open Chat Room
                        @if ($task->messages()->where('sender_id', '!=', Auth::id())->where('created_at', '>', now()->subDay())->count() > 0)
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse border border-white"></span>
                        @endif
                    </a>
                @else
                    {{-- JIKA MASIH QUEUE: TOMBOL CHAT TERKUNCI --}}
                    <button disabled
                        class="w-full py-3 bg-gray-100 text-gray-400 rounded-xl font-bold text-sm border border-gray-200 cursor-not-allowed flex items-center justify-center gap-2 relative z-10">
                        <i data-feather="lock" class="w-4 h-4"></i>
                        Chat Locked (Assign Staff First)
                    </button>
                @endif

                {{-- Decorative BG --}}
                <div
                    class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full z-0 group-hover:scale-150 transition-transform duration-500">
                </div>
            </div>

            {{-- 2. TIMELINE & ACTION CONTROL --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-lg mb-6">Progress Timeline</h3>

                {{-- Timeline UI --}}
                <div class="relative pl-4 border-l-2 border-gray-100 space-y-8 mb-8">

                    {{-- DOT 1: Created --}}
                    <div class="relative">
                        <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-black ring-4 ring-white"></div>
                        <p class="text-xs font-bold text-gray-900">Request Submitted</p>
                        <p class="text-[10px] text-gray-400">{{ $task->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    {{-- DOT 2: Current Status --}}
                    <div class="relative">
                        @php
                            $statusColor = match ($task->status) {
                                'queue' => 'bg-yellow-500',
                                'active', 'in_progress' => 'bg-blue-500',
                                'review' => 'bg-purple-500',
                                'completed' => 'bg-green-500',
                                'cancelled' => 'bg-red-500',
                                default => 'bg-gray-300',
                            };
                        @endphp
                        <div
                            class="absolute -left-[21px] top-1 w-3 h-3 rounded-full {{ $statusColor }} ring-4 ring-white animate-pulse">
                        </div>
                        <p class="text-xs font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $task->status) }}
                        </p>
                        <p class="text-[10px] text-gray-400">Current Stage</p>
                    </div>

                    {{-- DOT 3: Completion Target --}}
                    @if ($task->status != 'completed')
                        <div class="relative opacity-50">
                            <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-gray-200 ring-4 ring-white">
                            </div>
                            <p class="text-xs font-bold text-gray-400">Project Completion</p>
                            <p class="text-[10px] text-gray-400">Target</p>
                        </div>
                    @endif
                </div>

                {{-- DYNAMIC ACTION BUTTONS --}}
                <form action="{{ route('admin.projects.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Logic Tampilan Berdasarkan Status --}}
                    @if ($task->status == 'queue')

                        {{-- LOGIC CEK LIMIT SLOT CLIENT --}}
                        @php
                            $userTier = $task->user->wallet->tier ?? null;
                            $maxSlots = $userTier ? $userTier->max_active_tasks : 1; // Default 1 jika error

                            // Hitung project yang sedang berjalan (Active s/d Revision)
                            $runningCount = \App\Models\Task::where('user_id', $task->user_id)
                                ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
                                ->count();

                            $isSlotFull = $runningCount >= $maxSlots;
                        @endphp

                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-4">

                            {{-- Info Slot Header --}}
                            <div class="flex justify-between items-center mb-3 pb-3 border-b border-yellow-200">
                                <span class="text-[10px] font-bold uppercase text-yellow-800 tracking-widest">Client Active
                                    Slots</span>
                                <span class="text-xs font-bold {{ $isSlotFull ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $runningCount }} / {{ $maxSlots }} Used
                                </span>
                            </div>

                            @if ($isSlotFull)
                                {{-- Tampilan Jika Penuh --}}
                                <div class="bg-red-100 border border-red-200 rounded-lg p-3 mb-3 flex gap-2 items-start">
                                    <i data-feather="alert-triangle"
                                        class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-xs font-bold text-red-700">Limit Reached!</p>
                                        <p class="text-[10px] text-red-600 leading-tight mt-0.5">
                                            Client ({{ $userTier->name ?? 'User' }}) sudah mencapai batas project aktif.
                                            Selesaikan project lain sebelum memulai yang ini.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" disabled
                                    class="w-full py-3 bg-gray-200 text-gray-400 rounded-xl font-bold text-sm border border-gray-300 cursor-not-allowed flex justify-center items-center gap-2">
                                    <i data-feather="lock" class="w-4 h-4"></i>
                                    Start Project Locked
                                </button>
                            @else
                                {{-- Tampilan Jika Masih Ada Slot --}}
                                <p class="text-xs text-yellow-800 font-medium mb-3">Project ini siap untuk dikerjakan.
                                    Silakan pilih staff.</p>

                                <label
                                    class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Assign
                                    to Staff</label>
                                <div class="relative bg-white rounded-xl border border-gray-200 mb-4">
                                    <select name="assignee_id" required
                                        class="w-full bg-transparent p-3 pl-10 text-sm font-bold outline-none appearance-none cursor-pointer">
                                        <option value="">Select Staff...</option>
                                        @foreach ($staffMembers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-feather="user"
                                        class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                </div>
                                <input type="hidden" name="status" value="active">

                                <button type="submit"
                                    class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/20">
                                    Assign & Start Project
                                </button>
                            @endif
                        </div>
                    @elseif($task->status == 'active' || $task->status == 'in_progress')
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $task->assignee->avatar_url ?? 'https://ui-avatars.com/api/?name=Staff' }}"
                                    class="w-8 h-8 rounded-full border border-white">
                                <div>
                                    <p class="text-xs font-bold text-gray-900">Assigned to
                                        {{ $task->assignee->full_name ?? 'Staff' }}</p>
                                    <p class="text-[10px] text-gray-500">Working on it...</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="status" value="review">
                        <button type="submit"
                            class="w-full py-3 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/30">
                            Submit for Review
                        </button>
                    @elseif($task->status == 'review')
                        <div class="p-4 rounded-xl border border-gray-200 mb-4 text-center">
                            <p class="text-xs text-gray-500">Project sedang direview oleh client/admin.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit" name="status" value="revision"
                                class="py-3 bg-white border border-gray-200 text-black rounded-xl font-bold text-sm hover:bg-gray-50">
                                Needs Revision
                            </button>
                            <button type="submit" name="status" value="completed"
                                class="py-3 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 shadow-lg shadow-green-500/30">
                                Mark Completed
                            </button>
                        </div>
                    @elseif($task->status == 'completed')
                        <div class="p-6 bg-green-50 rounded-xl border border-green-100 text-center">
                            <div
                                class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-feather="check" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-green-800">Project Completed!</h4>
                            <p class="text-xs text-green-600 mt-1">Great job team.</p>
                        </div>
                    @endif

                </form>
            </div>

        </div>
    </div>
@endsection
