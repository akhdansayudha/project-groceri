@extends('client.layouts.app')

@section('content')
    {{-- UBAH CONTAINER JADI FULL WIDTH --}}
    <div class="w-full px-4 md:px-8 fade-in pb-20">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.requests.index') }}"
                    class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-black hover:text-white transition-colors shadow-sm">
                    <i data-feather="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $task->title }}</h1>
                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                        <span
                            class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">#{{ substr($task->id, 0, 8) }}</span>
                        <span>&bull;</span>
                        <span>{{ $task->workspace->name ?? 'Default Workspace' }}</span>
                        <span>&bull;</span>
                        <span>{{ $task->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS (Edit/Cancel for Queue) --}}
            @if ($task->status === 'queue')
                <div class="flex gap-2">
                    <a href="{{ route('client.requests.edit', $task->id) }}"
                        class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors shadow-sm">
                        Edit Details
                    </a>
                    <button type="button" onclick="confirmCancel('{{ $task->id }}')"
                        class="px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors">
                        Cancel Project
                    </button>
                    <form id="cancel-form-{{ $task->id }}" action="{{ route('client.requests.destroy', $task->id) }}"
                        method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI (Brief, Attachments, History) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- 1. UNIFIED PROJECT BRIEF CARD --}}
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                            <i data-feather="file-text" class="w-5 h-5 text-gray-400"></i> Project Specification
                        </h3>
                    </div>

                    <div class="p-8">
                        {{-- Description --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Brief &
                                Instruction</label>
                            <div
                                class="prose prose-sm max-w-none text-gray-700 leading-relaxed bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>

                        {{-- Additional Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if ($task->brief_data)
                                @foreach ($task->brief_data as $key => $value)
                                    <div>
                                        <label
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        <div class="font-medium text-gray-900 border-b border-gray-100 pb-2">
                                            {{ $value ?: '-' }}</div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-400 italic">No additional details provided.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. YOUR ATTACHMENTS --}}
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-8">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Your
                        Attachments</label>
                    @if (isset($task->attachments) && !empty($task->attachments) && isset($task->attachments['path']))
                        <div
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-2xl bg-gray-50 hover:bg-white hover:border-black transition-all group">
                            <div
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-gray-500 border border-gray-200 shadow-sm group-hover:text-black">
                                <i data-feather="file" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 truncate">
                                    {{ $task->attachments['original_name'] }}</p>
                                <span
                                    class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($task->attachments['uploaded_at'] ?? now())->diffForHumans() }}</span>
                            </div>
                            <a href="{{ Storage::disk('supabase')->url($task->attachments['path']) }}" target="_blank"
                                class="p-2.5 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors shadow-lg">
                                <i data-feather="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No files attached by you.</p>
                    @endif
                </div>

                {{-- 3. SUBMISSION HISTORY (HASIL KERJA STAFF) --}}
                @if ($task->status !== 'queue')
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                        <div
                            class="px-8 py-6 border-b border-gray-100 bg-black text-white flex justify-between items-center">
                            <h3 class="font-bold text-lg flex items-center gap-2">
                                <i data-feather="layers" class="w-5 h-5"></i> Submission History
                            </h3>
                            <span class="text-xs font-medium bg-white/20 px-2 py-1 rounded">From Staff</span>
                        </div>

                        <div class="p-8 space-y-4">
                            @forelse($task->deliverables as $deliv)
                                <div
                                    class="relative pl-6 border-l-2 {{ $loop->first ? 'border-green-500' : 'border-gray-200' }} pb-2">
                                    <div
                                        class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $loop->first ? 'bg-green-500 border-4 border-white shadow' : 'bg-gray-200 border-2 border-white' }}">
                                    </div>

                                    <div
                                        class="bg-gray-50 rounded-2xl p-4 border border-gray-100 hover:border-gray-300 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-xs font-bold text-gray-900 uppercase">Version
                                                    {{ $loop->count - $loop->index }}</p>
                                                <p class="text-[10px] text-gray-500">
                                                    {{ $deliv->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                            <a href="{{ $deliv->file_type == 'file' ? Storage::disk('supabase')->url($deliv->file_url) : $deliv->file_url }}"
                                                target="_blank"
                                                class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold hover:bg-black hover:text-white transition-colors shadow-sm flex items-center gap-2">
                                                View <i data-feather="external-link" class="w-3 h-3"></i>
                                            </a>
                                        </div>
                                        @if ($deliv->message)
                                            <div
                                                class="text-sm text-gray-600 bg-white p-3 rounded-xl border border-gray-100 italic">
                                                "{{ $deliv->message }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i data-feather="clock" class="w-5 h-5 text-gray-400"></i>
                                    </div>
                                    <p class="text-sm text-gray-500">Staff hasn't submitted any work yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN (Service, Staff, Actions) --}}
            <div class="space-y-6">

                {{-- 1. SERVICE INFO --}}
                <div class="bg-[#111] text-white p-6 rounded-3xl shadow-xl relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-10 -mt-10">
                    </div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/10">
                            {{-- FIX: Service Icon --}}
                            <img src="{{ Storage::disk('supabase')->url($task->service->icon_url) }}"
                                class="w-6 h-6 invert brightness-0">
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1 tracking-widest">Service Package
                            </p>
                            <h3 class="font-bold text-lg">{{ $task->service->name }}</h3>
                            <p class="text-xs text-gray-400 mt-1">Locked Price: <span
                                    class="text-white font-bold">{{ $task->toratix_locked }} Tokens</span></p>
                        </div>
                    </div>
                </div>

                {{-- 2. REVIEW ACTION CENTER (Hanya muncul jika status REVIEW) --}}
                @if ($task->status === 'review')
                    <div class="bg-white p-6 rounded-3xl border border-blue-200 shadow-lg shadow-blue-50">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                            <h3 class="font-bold text-gray-900">Review Required</h3>
                        </div>

                        <p class="text-xs text-gray-600 mb-6 leading-relaxed">
                            Staff telah mengirimkan hasil kerja. Silakan periksa di bagian "Submission History".
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- REVISION BUTTON --}}
                            <button onclick="toggleRevisionForm()"
                                class="py-3 px-4 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-xs hover:border-black hover:text-black transition-all">
                                Request Revision
                            </button>

                            {{-- ACCEPT BUTTON --}}
                            <form action="{{ route('client.requests.complete', $task->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-3 px-4 bg-black text-white rounded-xl font-bold text-xs hover:bg-gray-800 transition-all shadow-lg">
                                    Accept Work
                                </button>
                            </form>
                        </div>

                        {{-- HIDDEN REVISION FORM --}}
                        <div id="revision-form" class="hidden mt-4 pt-4 border-t border-gray-100 animate-fade-in-down">
                            <form action="{{ route('client.requests.revision', $task->id) }}" method="POST">
                                @csrf
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Revision
                                    Notes</label>
                                <textarea name="revision_notes" rows="3"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-black focus:border-black mb-3"
                                    placeholder="Explain what needs to be changed..." required></textarea>
                                <div class="text-xs text-gray-500 mb-3 italic">
                                    <i data-feather="info" class="w-3 h-3 inline"></i> You have <strong>1x</strong>
                                    revision quota.
                                </div>
                                <button type="submit"
                                    class="w-full py-3 bg-red-600 text-white rounded-xl font-bold text-xs hover:bg-red-700 transition-all">
                                    Submit Revision Request
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- 3. ASSIGNED STAFF CARD (Style Staff View) --}}
                @if ($task->assignee)
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-4 mb-6">
                            {{-- Avatar Staff --}}
                            <div class="relative">
                                <img src="{{ $task->assignee->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->assignee->full_name }}"
                                    class="w-12 h-12 rounded-full border border-gray-100 object-cover">

                                @if ($isStaffOnline)
                                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full animate-pulse"
                                        title="Online"></div>
                                @else
                                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-gray-300 border-2 border-white rounded-full"
                                        title="Offline"></div>
                                @endif
                            </div>

                            {{-- Nama Staff --}}
                            <div>
                                <p class="text-xs font-bold uppercase text-gray-400 mb-0.5">Assigned Creative</p>
                                <p class="font-bold text-gray-900 text-sm">{{ $task->assignee->full_name }}</p>

                                <div class="flex items-center gap-1.5 mt-1">
                                    @if ($isStaffOnline)
                                        <span
                                            class="text-[10px] font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Online
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-wide flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span> Offline
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('client.requests.chat', $task->id) }}"
                            class="w-full py-3 bg-white border border-gray-200 text-black rounded-xl font-bold text-sm hover:bg-black hover:text-white transition-all flex items-center justify-center gap-2">
                            <i data-feather="message-circle" class="w-4 h-4"></i>
                            Open Room Chat
                        </a>
                    </div>
                @else
                    {{-- WAITING FOR ASSIGNEE --}}
                    <div class="bg-yellow-50 p-6 rounded-3xl border border-yellow-100 text-center">
                        <div
                            class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                            <i data-feather="user" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-yellow-800 text-sm">Matching Staff...</h4>
                        <p class="text-xs text-yellow-700 mt-1">We are assigning the best creative for your project.</p>
                    </div>
                @endif

                {{-- 4. TIMELINE (REAL TIME DATA) --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i data-feather="activity" class="w-4 h-4 text-gray-400"></i> Project Progress
                    </h3>

                    @php
                        // 1. Mapping Status ke Kolom Tanggal Database
                        $stages = [
                            [
                                'key' => 'queue',
                                'label' => 'Queue',
                                'date' => $task->created_at,
                            ],
                            [
                                'key' => 'active',
                                'label' => 'Active',
                                'date' => $task->active_at, // Kolom baru
                            ],
                            [
                                'key' => 'in_progress',
                                'label' => 'In Progress',
                                'date' => $task->started_at,
                            ],
                            [
                                'key' => 'review',
                                'label' => 'Review',
                                'date' => $task->review_at, // Kolom baru
                            ],
                            [
                                'key' => 'completed',
                                'label' => 'Done',
                                'date' => $task->completed_at,
                            ],
                        ];

                        // 2. Tentukan Index Status Saat Ini
                        $statusMap = ['queue', 'active', 'in_progress', 'review', 'completed'];
                        // Status 'revision' dianggap setara posisi 'review' (index 3)
                        $currentStatusKey = $task->status == 'revision' ? 'in_progress' : $task->status;
                        $currentIdx = array_search($currentStatusKey, $statusMap);
                        if ($currentIdx === false) {
                            $currentIdx = 0;
                        }
                    @endphp

                    <div class="space-y-0 pl-1">
                        @foreach ($stages as $index => $stage)
                            @php
                                // Cek apakah tahap ini sudah dilewati/aktif
                                $isPassed = $index <= $currentIdx;
                            @endphp

                            <div class="flex gap-4 relative pb-8 last:pb-0">

                                {{-- GARIS PENGHUBUNG --}}
                                @if (!$loop->last)
                                    {{-- Garis Abu (Background) --}}
                                    <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-gray-100 z-0"></div>

                                    {{-- Garis Hitam (Active) - Muncul jika tahap SELANJUTNYA sudah dicapai --}}
                                    @if ($index < $currentIdx)
                                        <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-black z-0"></div>
                                    @endif
                                @endif

                                {{-- DOT INDICATOR --}}
                                <div
                                    class="relative z-10 w-4 h-4 rounded-full border-2 {{ $isPassed ? 'bg-black border-black scale-110' : 'bg-white border-gray-300' }} flex-shrink-0 mt-1 transition-all">
                                </div>

                                {{-- LABEL & WAKTU --}}
                                <div class="-mt-0.5">
                                    <h4
                                        class="text-xs font-bold uppercase {{ $isPassed ? 'text-black' : 'text-gray-400' }}">
                                        {{ $stage['label'] }}
                                    </h4>

                                    {{-- TAMPILKAN WAKTU REAL (Jika Ada Datanya & Sudah Dilewati) --}}
                                    @if ($isPassed && $stage['date'])
                                        <p class="text-[10px] text-gray-400 mt-1 font-mono">
                                            {{ \Carbon\Carbon::parse($stage['date'])->format('d M, H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Pastikan SweetAlert2 sudah diload di layout utama, jika belum, uncomment baris bawah --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fungsi untuk toggle form revisi (jika ada)
        function toggleRevisionForm() {
            const form = document.getElementById('revision-form');
            form.classList.toggle('hidden');
        }

        // FUNGSI CONFIRM CANCEL DENGAN TEMA VEKTORA
        function confirmCancel(taskId) {
            Swal.fire({
                // 1. Konten & Icon
                title: 'Cancel Project?',
                // Menggunakan HTML untuk menonjolkan pesan refund
                html: `
                <div class="flex flex-col gap-2 mt-3">
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Are you sure you want to cancel this request? This action is irreversible.
                    </p>
                    {{-- Highlight Refund Info --}}
                    <div class="bg-green-50 border border-green-100 rounded-xl p-3 flex items-center gap-3 text-left animate-pulse">
                        <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-green-700 tracking-wider">Refund Guarantee</p>
                            <p class="text-xs font-bold text-green-900">100% Tokens will be refunded to your wallet.</p>
                        </div>
                    </div>
                </div>
            `,
                icon: 'warning',
                // Warna icon warning disesuaikan dengan merah Vektora agar lebih 'waspada'
                iconColor: '#dc2626', // Tailwind red-600

                // 2. Konfigurasi Tombol
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel Project',
                cancelButtonText: 'No, Keep Project',
                reverseButtons: true, // Tombol Cancel di kiri (abu), Confirm di kanan (merah)
                focusCancel: true, // Fokus default ke tombol 'No' demi keamanan

                // 3. KUSTOMISASI TEMA VEKTORA (PENTING)
                buttonsStyling: false, // Matikan style bawaan SweetAlert
                customClass: {
                    // Container Modal
                    popup: 'rounded-3xl shadow-2xl border border-gray-100 font-sans p-0 overflow-hidden',
                    // Header
                    title: 'text-gray-900 font-bold text-xl pt-8 px-8',
                    // Body Content
                    htmlContainer: 'px-8 pb-4',
                    // Icon Warning (Diberi background merah muda halus)
                    icon: 'border-red-100 bg-red-50 text-red-600 scale-90 mt-8 mx-auto mb-2',
                    // Tombol Confirm (Merah Vektora - Destructive Action)
                    confirmButton: 'w-full sm:w-auto bg-red-600 text-white rounded-xl font-bold text-sm px-6 py-3.5 hover:bg-red-700 focus:ring-4 focus:ring-red-200 transition-all shadow-lg shadow-red-500/30',
                    // Tombol Cancel (Outline Gray Vektora - Safe Action)
                    cancelButton: 'w-full sm:w-auto bg-white text-gray-700 border border-gray-200 rounded-xl font-bold text-sm px-6 py-3.5 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 focus:ring-4 focus:ring-gray-100 transition-all',
                    // Container Tombol
                    actions: 'gap-3 px-8 pb-8 w-full flex-col-reverse sm:flex-row',
                },
                width: '420px', // Sedikit lebih ramping agar elegan

            }).then((result) => {
                if (result.isConfirmed) {
                    // Efek loading saat dikonfirmasi sebelum submit
                    Swal.showLoading();
                    document.getElementById('cancel-form-' + taskId).submit();
                }
            });
        }
    </script>
@endsection
