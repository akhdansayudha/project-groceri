@extends('client.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto fade-in">

        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('client.requests.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-black transition-colors font-bold text-sm">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Requests
            </a>

            @if ($task->status === 'queue')
                <div class="flex gap-2">
                    <a href="{{ route('client.requests.edit', $task->id) }}"
                        class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors">
                        Edit Details
                    </a>

                    <button type="button" onclick="confirmCancel('{{ $task->id }}')"
                        class="px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">
                        Cancel Project
                    </button>

                    <form id="cancel-form-{{ $task->id }}" action="{{ route('client.requests.destroy', $task->id) }}"
                        method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">

                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Project Title</p>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $task->title }}</h1>
                        <div class="flex items-center gap-3 mt-2">
                            <p class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-1 rounded">ID:
                                #{{ substr($task->id, 0, 8) }}</p>
                            <span class="flex items-center gap-1 text-xs font-bold text-gray-500">
                                <i data-feather="folder" class="w-3 h-3"></i>
                                {{ $task->workspace->name ?? 'Unassigned' }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-black text-white rounded-xl flex items-center justify-center shadow-lg shadow-gray-200">
                            <i data-feather="calendar" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Target Deadline</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y') }}
                            </p>
                            <p class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($task->deadline)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <div class="group">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Description /
                            Brief</label>
                        <div
                            class="w-full border border-gray-200 rounded-2xl p-6 text-sm bg-gray-50/50 leading-relaxed text-gray-700">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100">
                    <p class="text-sm font-bold mb-4 flex items-center gap-2">
                        <i data-feather="sliders" class="w-4 h-4"></i> Additional Details
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @if ($task->brief_data)
                            @foreach ($task->brief_data as $key => $value)
                                <div>
                                    <label
                                        class="text-xs text-gray-500 font-semibold uppercase">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                    <div
                                        class="w-full border border-gray-200 rounded-lg p-3 text-sm mt-1 bg-white font-medium text-gray-900">
                                        {{ $value ?: '-' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-400 italic">No additional details provided.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Attachments</label>

                    {{-- Cek apakah ada data attachment yang valid (memiliki path) --}}
                    @if (isset($task->attachments) && !empty($task->attachments) && isset($task->attachments['path']))
                        <div
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-2xl bg-gray-50 hover:bg-white hover:border-black transition-all group">

                            {{-- Icon File --}}
                            <div
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-gray-500 border border-gray-200 shadow-sm group-hover:text-black">
                                @php
                                    $mime = $task->attachments['mime_type'] ?? '';
                                @endphp

                                @if (str_contains($mime, 'image'))
                                    <i data-feather="image" class="w-6 h-6"></i>
                                @elseif(str_contains($mime, 'pdf'))
                                    <i data-feather="file-text" class="w-6 h-6"></i>
                                @else
                                    <i data-feather="file" class="w-6 h-6"></i>
                                @endif
                            </div>

                            {{-- File Info --}}
                            <div class="flex-1 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 truncate"
                                    title="{{ $task->attachments['original_name'] }}">
                                    {{ $task->attachments['original_name'] }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-mono text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded">
                                        {{ isset($task->attachments['size']) ? number_format($task->attachments['size'] / 1024, 1) . ' KB' : 'Unknown Size' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        {{ \Carbon\Carbon::parse($task->attachments['uploaded_at'] ?? now())->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Download Button --}}
                            <a href="{{ Storage::disk('supabase')->url($task->attachments['path']) }}" target="_blank"
                                class="p-2.5 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors shadow-lg shadow-black/20"
                                title="Download File">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @else
                        {{-- TAMPILAN JIKA TIDAK ADA FILE --}}
                        <div
                            class="border-2 border-dashed border-gray-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center">
                            <div
                                class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                <i data-feather="file-minus" class="w-6 h-6"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-400">Tidak ada file lampiran.</p>
                        </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-[#111] text-white p-6 rounded-3xl shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-800 rounded-2xl flex items-center justify-center text-white">
                            @if ($task->service->icon_url)
                                <img src="{{ $task->service->icon_url }}" class="w-6 h-6">
                            @else
                                <i data-feather="zap" class="w-6 h-6"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Service Type</p>
                            <h3 class="font-bold text-lg">{{ $task->service->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Cost: {{ $task->toratix_locked }} TX</p>
                        </div>
                    </div>
                </div>

                @if ($task->status !== 'queue')
                    <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm text-center">
                        <div
                            class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-feather="message-circle" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Need updates?</h3>
                        <p class="text-xs text-gray-500 mb-4 mt-1">Chat directly with the assigned creative staff.</p>

                        <a href="{{ route('client.requests.chat', $task->id) }}"
                            class="block w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                            Chat with Staff
                        </a>
                    </div>
                @else
                    <div class="bg-yellow-50 p-6 rounded-3xl border border-yellow-100 text-yellow-800">
                        <div class="flex items-center gap-2 font-bold mb-2">
                            <i data-feather="clock" class="w-4 h-4"></i>
                            <span>In Queue</span>
                        </div>
                        <p class="text-xs opacity-80 leading-relaxed">
                            Project Anda sedang dalam antrian. Tim kami akan segera meninjau dan menugaskan staff.
                            Anda masih bisa mengubah detail atau membatalkan project ini.
                        </p>
                    </div>
                @endif

                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i data-feather="activity" class="w-4 h-4 text-gray-400"></i>
                        Project Progress
                    </h3>

                    @php
                        $steps = ['queue', 'active', 'in_progress', 'review', 'completed'];
                        $labels = ['Queue', 'Active', 'In Progress', 'Review', 'Done'];
                        $currentIdx = array_search($task->status, $steps);
                        if ($currentIdx === false && $task->status == 'revision') {
                            $currentIdx = 3;
                        }
                    @endphp

                    <div class="space-y-0 pl-1">
                        @foreach ($steps as $index => $step)
                            <div class="flex gap-4 relative pb-8 last:pb-0 group">

                                @if (!$loop->last)
                                    <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-gray-100 z-0"></div>
                                    @if ($index < $currentIdx)
                                        <div class="absolute left-[7px] top-3 bottom-0 w-[2px] bg-black z-0"></div>
                                    @endif
                                @endif

                                <div
                                    class="relative z-10 w-4 h-4 rounded-full border-2 {{ $index <= $currentIdx ? 'bg-black border-black scale-110' : 'bg-white border-gray-300' }} flex-shrink-0 mt-1 transition-all">
                                </div>

                                <div class="-mt-0.5">
                                    <h4
                                        class="text-xs font-bold uppercase {{ $index <= $currentIdx ? 'text-black' : 'text-gray-400' }}">
                                        {{ $labels[$index] }}
                                    </h4>

                                    {{-- Tanggal hanya muncul di step pertama (Queue) dan step aktif saat ini --}}
                                    @if ($index == 0)
                                        <p class="text-[10px] text-gray-400 mt-1 font-medium">
                                            {{ $task->created_at->format('d M, H:i') }}</p>
                                    @elseif($index == $currentIdx)
                                        <p class="text-[10px] text-gray-400 mt-1 font-medium">
                                            {{ $task->updated_at->format('d M, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script Confirm Cancel --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmCancel(taskId) {
            Swal.fire({
                title: 'Batalkan Project?',
                text: "{{ $task->toratix_locked }} Token akan dikembalikan ke wallet Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-4 py-2',
                    cancelButton: 'rounded-xl px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancel-form-' + taskId).submit();
                }
            })
        }
    </script>
@endsection
