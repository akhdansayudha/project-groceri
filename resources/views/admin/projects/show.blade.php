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
                        class="px-2.5 py-1 rounded-md bg-black text-white text-[10px] font-mono font-bold uppercase tracking-wider">
                        {{ $task->service->name }}
                    </span>
                </div>
                <p class="text-gray-500 text-xs mt-1">Submitted on {{ $task->created_at->format('d F Y, H:i') }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            @if ($task->status == 'queue')
                <button onclick="openDeleteModal()"
                    class="px-4 py-2 bg-white border border-red-200 text-red-500 rounded-xl text-xs font-bold hover:bg-red-50 hover:border-red-300 transition-colors flex items-center gap-2 shadow-sm">
                    <i data-feather="trash-2" class="w-4 h-4"></i> Delete Project
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-2 fade-in">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200 fade-in">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- KOLOM KIRI: FULL DETAILS & SUBMISSION --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Project Specification --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="file-text" class="w-5 h-5 text-gray-500"></i> Project Specification
                    </h3>
                </div>

                <div class="p-8 space-y-6">
                    {{-- Judul Project --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Project
                            Title</label>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-900 font-bold text-lg">
                            {{ $task->title ?? '-' }}
                        </div>
                    </div>

                    {{-- Service & Budget --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Service
                                Category</label>
                            <input type="text" readonly value="{{ $task->service->name ?? '-' }}"
                                class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 text-sm focus:outline-none font-medium">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Budget /
                                Locked</label>
                            <div class="relative">
                                <input type="text" readonly value="{{ $task->toratix_locked ?? 0 }} Tokens"
                                    class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-3 pl-11 text-sm focus:outline-none font-bold">
                                <i data-feather="zap"
                                    class="w-4 h-4 text-yellow-500 absolute left-4 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>
                    </div>

                    {{-- BRIEF DATA SECTION (NEW) --}}
                    @php
                        // Decode JSON brief_data (handling string atau array)
                        $brief = $task->brief_data;
                        if (is_string($brief)) {
                            $brief = json_decode($brief, true);
                        }
                        // Pastikan array agar tidak error saat akses key
                        $brief = $brief ?? [];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                        {{-- 1. Color Tone --}}
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Color
                                Tone</label>
                            <div
                                class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3 h-[46px]">
                                @if (!empty($brief['color']))
                                    {{-- Tampilkan kotak warna jika format hex, jika tidak tampilkan text saja --}}
                                    <div class="w-5 h-5 rounded border border-gray-300 shadow-sm flex-shrink-0"
                                        style="background-color: {{ $brief['color'] }}"></div>
                                    <span class="text-sm font-bold text-gray-700 truncate">{{ $brief['color'] }}</span>
                                @else
                                    <span class="text-sm font-bold text-gray-400">-</span>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Audience --}}
                        <div>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Target
                                Audience</label>
                            <div
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 h-[46px] flex items-center">
                                <span
                                    class="text-sm font-bold {{ !empty($brief['audience']) ? 'text-gray-700' : 'text-gray-400' }}">
                                    {{ $brief['audience'] ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- 3. Reference Link --}}
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Reference</label>
                            <div
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 h-[46px] flex items-center overflow-hidden">
                                @if (!empty($brief['reference']))
                                    <a href="{{ $brief['reference'] }}" target="_blank"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline truncate flex items-center gap-2 w-full">
                                        <i data-feather="external-link" class="w-3 h-3 flex-shrink-0"></i>
                                        {{ $brief['reference'] }}
                                    </a>
                                @else
                                    <span class="text-sm font-bold text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="pt-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Detailed
                            Brief</label>
                        <div
                            class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-5 text-sm leading-relaxed min-h-[100px]">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Attachments (Client Files) --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="paperclip" class="w-5 h-5 text-gray-500"></i> Client Attachments
                    </h3>
                </div>
                <div class="p-8">
                    @php
                        $file = is_string($task->attachments)
                            ? json_decode($task->attachments, true)
                            : $task->attachments;
                    @endphp

                    @if (isset($file) && !empty($file) && isset($file['path']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($file['path']) }}"
                                target="_blank"
                                class="group flex items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-black hover:shadow-md transition-all bg-white">
                                <div
                                    class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-black group-hover:text-white transition-colors border border-gray-200">
                                    <i data-feather="file" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:underline">
                                        {{ $file['original_name'] }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase">Click to view</p>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">
                            <p class="text-gray-400 text-sm">No reference files attached.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. SUBMISSION HISTORY (Hasil Kerja Staff) --}}
            @if ($task->status != 'queue')
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in">
                    <div class="px-8 py-6 border-b border-gray-100 bg-black text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <i data-feather="layers" class="w-5 h-5"></i> Submission History
                        </h3>
                        <span class="text-xs font-medium bg-white/20 px-2 py-1 rounded">From Staff</span>
                    </div>

                    <div class="p-8 space-y-6">
                        @forelse($task->deliverables as $deliv)
                            <div
                                class="relative pl-6 border-l-2 {{ $loop->first ? 'border-green-500' : 'border-gray-200' }}">
                                <div
                                    class="absolute -left-[5px] top-0 w-2 h-2 rounded-full {{ $loop->first ? 'bg-green-500' : 'bg-gray-300' }}">
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase">{{ $deliv->created_at->format('d M Y, H:i') }}</span>
                                            <p class="text-xs font-bold text-gray-900 mt-0.5">Submitted by
                                                {{ $deliv->staff->full_name ?? 'Admin' }}</p>
                                        </div>
                                        @php
                                            $fileLink =
                                                $deliv->file_type == 'file'
                                                    ? \Illuminate\Support\Facades\Storage::disk('supabase')->url(
                                                        $deliv->file_url,
                                                    )
                                                    : $deliv->file_url;
                                        @endphp
                                        <a href="{{ $fileLink }}" target="_blank"
                                            class="text-[10px] bg-white border border-gray-200 px-3 py-1.5 rounded-lg font-bold hover:bg-black hover:text-white transition-colors">
                                            View {{ $deliv->file_type == 'file' ? 'File' : 'Link' }}
                                        </a>
                                    </div>
                                    @if ($deliv->message)
                                        <p
                                            class="text-xs text-gray-600 italic bg-white p-2 rounded border border-gray-100 mt-2">
                                            "{{ $deliv->message }}"</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-gray-400 text-sm">Belum ada submission dari staff.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>

        {{-- KOLOM KANAN: ACTION & CONTROL & TIMELINE --}}
        <div class="space-y-6">

            {{-- 1. CLIENT CARD --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm relative overflow-hidden group">
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                        class="w-14 h-14 rounded-full border-2 border-white shadow-md object-cover">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Project Owner</p>
                        <p class="font-bold text-gray-900 text-lg leading-tight">{{ $task->user->full_name }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if ($isUserOnline ?? false)
                                <span class="text-[10px] text-green-600 font-bold flex items-center gap-1"><span
                                        class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Online</span>
                            @else
                                <span class="text-[10px] text-gray-400 font-bold flex items-center gap-1"><span
                                        class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span> Offline</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($task->status !== 'queue')
                    <a href="{{ route('admin.projects.chat', $task->id) }}"
                        class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-black/20 relative z-10">
                        <i data-feather="message-circle" class="w-4 h-4"></i> Open Chat Room
                    </a>
                @else
                    <button disabled
                        class="w-full py-3 bg-gray-100 text-gray-400 rounded-xl font-bold text-sm border border-gray-200 cursor-not-allowed flex items-center justify-center gap-2 relative z-10">
                        <i data-feather="lock" class="w-4 h-4"></i> Chat Locked
                    </button>
                @endif
            </div>

            {{-- 2. TIMELINE PROGRESS (Dipindah ke Kanan) --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-feather="activity" class="w-4 h-4 text-gray-400"></i> Project Progress
                </h3>
                @php
                    $stages = [
                        ['key' => 'queue', 'label' => 'Queue', 'date' => $task->created_at],
                        ['key' => 'active', 'label' => 'Active', 'date' => $task->active_at],
                        ['key' => 'in_progress', 'label' => 'In Progress', 'date' => $task->started_at],
                        ['key' => 'review', 'label' => 'Review', 'date' => $task->review_at],
                        ['key' => 'completed', 'label' => 'Done', 'date' => $task->completed_at],
                    ];
                    $statusMap = ['queue', 'active', 'in_progress', 'review', 'completed'];
                    $currentStatusKey = $task->status == 'revision' ? 'in_progress' : $task->status;
                    $currentIdx = array_search($currentStatusKey, $statusMap);
                    if ($currentIdx === false) {
                        $currentIdx = 0;
                    }
                @endphp

                <div class="space-y-0 pl-1">
                    @foreach ($stages as $index => $stage)
                        @php $isPassed = $index <= $currentIdx; @endphp
                        <div class="flex gap-4 relative pb-8 last:pb-0">
                            {{-- Lines --}}
                            @if (!$loop->last)
                                <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-gray-100 z-0"></div>
                                @if ($index < $currentIdx)
                                    <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-black z-0"></div>
                                @endif
                            @endif
                            {{-- Dot --}}
                            <div
                                class="relative z-10 w-4 h-4 rounded-full border-2 {{ $isPassed ? 'bg-black border-black scale-110' : 'bg-white border-gray-300' }} flex-shrink-0 mt-1 transition-all">
                            </div>
                            {{-- Text --}}
                            <div class="-mt-0.5">
                                <h4 class="text-xs font-bold uppercase {{ $isPassed ? 'text-black' : 'text-gray-400' }}">
                                    {{ $stage['label'] }}</h4>
                                @if ($isPassed && $stage['date'])
                                    <p class="text-[10px] text-gray-400 mt-1 font-mono">
                                        {{ \Carbon\Carbon::parse($stage['date'])->format('d M, H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. DYNAMIC ACTION CONTROL (GOD MODE) --}}

            {{-- CASE A: QUEUE (Assign Staff) --}}
            @if ($task->status == 'queue')
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-lg mb-4">Assign Staff</h3>
                    <form action="{{ route('admin.projects.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-4">
                            <p class="text-xs text-yellow-800 font-medium mb-3">Project siap dikerjakan. Pilih staff untuk
                                memulai.</p>
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Assign
                                to</label>
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
                        </div>
                    </form>
                </div>

                {{-- CASE B: IN PROGRESS / REVISION (Admin Submit Work) --}}
            @elseif(in_array($task->status, ['in_progress', 'revision', 'active']))
                <div
                    class="bg-white rounded-3xl border border-gray-200 shadow-lg shadow-blue-900/5 relative overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-black text-white">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <i data-feather="send" class="w-4 h-4"></i> Admin Submission
                        </h3>
                    </div>
                    <div class="p-6">
                        {{-- Jika Status Active, tombol start --}}
                        @if ($task->status == 'active')
                            <form action="{{ route('admin.projects.update', $task->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="in_progress">
                                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center">
                                    <p class="text-xs text-blue-800 mb-3 font-bold">Project assigned but not started.</p>
                                    <button type="submit"
                                        class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm">
                                        Force Start Project
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- Form Submit Work --}}
                            @if ($task->status == 'revision')
                                <div
                                    class="bg-orange-50 border border-orange-100 p-3 rounded-xl mb-4 text-xs text-orange-700 flex gap-2">
                                    <i data-feather="alert-circle" class="w-4 h-4"></i>
                                    Client requested revision.
                                </div>
                            @endif

                            <form action="{{ route('admin.projects.submit', $task->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Upload
                                            File</label>
                                        <input type="file" name="submission_file"
                                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-xl cursor-pointer">
                                    </div>
                                    <div class="text-center text-xs text-gray-400 font-bold uppercase">- OR -</div>
                                    <div>
                                        <label
                                            class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">External
                                            Link</label>
                                        <input type="url" name="submission_link" placeholder="e.g. Figma, Drive..."
                                            class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-black transition-all">
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Notes</label>
                                        <textarea name="message" rows="2" placeholder="Submission notes..."
                                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-black transition-all"></textarea>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-3.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg">
                                        Submit for Review (As Admin)
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- CASE C: REVIEW (Admin Approve/Reject) --}}
            @elseif($task->status == 'review')
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm" x-data="{ showRevision: false }">
                    <h3 class="font-bold text-lg mb-4">Review Submission</h3>
                    <div class="bg-purple-50 p-4 rounded-xl border border-purple-100 mb-6 text-center">
                        <p class="text-xs text-purple-800">Project currently in review. Admin can force action.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3" x-show="!showRevision">
                        <button type="button" @click="showRevision = true"
                            class="py-3 bg-white border border-gray-200 text-black rounded-xl font-bold text-sm hover:bg-gray-50">
                            Request Revision
                        </button>

                        <form action="{{ route('admin.projects.update', $task->id) }}" method="POST" class="w-full">
                            @csrf @method('PUT')
                            <button type="submit" name="status" value="completed"
                                class="w-full py-3 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 shadow-lg shadow-green-500/30">
                                Accept Work
                            </button>
                        </form>
                    </div>

                    {{-- REVISION FORM (Hidden by default) --}}
                    <div x-show="showRevision" class="mt-4 border-t border-gray-100 pt-4" style="display: none;">
                        <form action="{{ route('admin.projects.update', $task->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="revision">

                            <label
                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Revision
                                Notes</label>
                            <textarea name="revision_notes" rows="3" required placeholder="Explain what needs to be fixed..."
                                class="w-full bg-red-50 border border-red-100 rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-200 transition-all mb-3"></textarea>

                            <div class="flex gap-2">
                                <button type="button" @click="showRevision = false"
                                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200">Cancel</button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold text-xs hover:bg-red-700">Send
                                    Revision</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- CASE D: COMPLETED --}}
            @elseif($task->status == 'completed')
                <div class="p-6 bg-green-50 rounded-3xl border border-green-100 text-center">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i data-feather="check" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-green-800 text-lg">Project Completed!</h4>
                    <p class="text-sm text-green-600 mt-1">Great job team.</p>
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl p-8 shadow-2xl transform transition-all scale-100">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100">
                    <i data-feather="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Project?</h3>
                <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                    Are you sure you want to delete this project? This action cannot be undone.
                </p>

                {{-- Project Info Summary --}}
                <div class="bg-gray-50 rounded-xl p-4 text-left border border-gray-100 mb-6 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Project Name:</span>
                        <span class="font-bold text-gray-900 truncate max-w-[150px]">{{ $task->title }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Client:</span>
                        <span class="font-bold text-gray-900">{{ $task->user->full_name }}</span>
                    </div>
                    <div class="flex justify-between text-xs pt-2 border-t border-gray-200 mt-2">
                        <span class="text-gray-500">Refund Amount:</span>
                        <span class="font-bold text-green-600">+{{ $task->toratix_locked }} Tokens</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <form action="{{ route('admin.projects.destroy', $task->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30">
                            Confirm Delete
                        </button>
                    </form>
                </div>
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
    </script>
@endsection
