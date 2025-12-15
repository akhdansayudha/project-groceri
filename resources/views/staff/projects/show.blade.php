@extends('staff.layouts.app')

@section('content')
    {{-- HEADER & NAVIGATION --}}
    <div class="mb-6 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.projects.index') }}"
                class="p-2.5 bg-white border border-gray-200 rounded-xl hover:bg-black hover:text-white transition-colors shadow-sm">
                <i data-feather="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Project #{{ substr($task->id, 0, 8) }}</h1>
                </div>

                {{-- BADGES: Service Name & Token Locked --}}
                <div class="flex items-center gap-2">
                    {{-- Service Name --}}
                    <span
                        class="px-3 py-1 rounded-full bg-black text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                        {{ $task->service->name }}
                    </span>

                    {{-- Token Earnings --}}
                    <span
                        class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px] font-bold uppercase tracking-wider shadow-sm flex items-center gap-1"
                        title="Estimated Earnings">
                        <i data-feather="zap" class="w-3 h-3 fill-yellow-500 text-yellow-500"></i>
                        {{ $task->toratix_locked }} Tokens
                    </span>
                </div>

                <p class="text-gray-500 text-xs mt-2">
                    Assigned on {{ $task->updated_at->format('d F Y, H:i') }}
                </p>
            </div>
        </div>

        {{-- ACTION BUTTON: START PROJECT --}}
        @if ($task->status == 'active')
            <form action="{{ route('staff.projects.start', $task->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                    <i data-feather="play" class="w-4 h-4 fill-current"></i>
                    Start Working
                </button>
            </form>
        @endif
    </div>

    {{-- GLOBAL ALERTS --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl fade-in">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i data-feather="alert-circle" class="w-4 h-4"></i>
                <span>Action Failed</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl fade-in flex items-center gap-2">
            <i data-feather="x-circle" class="w-4 h-4"></i>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div
            class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl fade-in flex items-center gap-2">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif


    {{-- TIMELINE PROGRESS (Horizontal - Real Time Data) --}}
    <div class="mb-8 fade-in">
        <div class="bg-white border border-gray-200 rounded-3xl p-8 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-8 flex items-center gap-2">
                <i data-feather="activity" class="w-4 h-4 text-gray-400"></i>
                Project Progress
            </h3>

            @php
                $stages = [
                    ['key' => 'queue', 'label' => 'QUEUE', 'date' => $task->created_at],
                    ['key' => 'active', 'label' => 'ACTIVE', 'date' => $task->active_at],
                    ['key' => 'in_progress', 'label' => 'IN PROGRESS', 'date' => $task->started_at],
                    ['key' => 'review', 'label' => 'REVIEW', 'date' => $task->review_at],
                    ['key' => 'completed', 'label' => 'DONE', 'date' => $task->completed_at],
                ];
                $statusMap = ['queue', 'active', 'in_progress', 'review', 'completed'];
                $currentStatusKey = $task->status == 'revision' ? 'in_progress' : $task->status;
                $currentIdx = array_search($currentStatusKey, $statusMap);
                if ($currentIdx === false) {
                    $currentIdx = 0;
                }
            @endphp

            <div class="relative px-4">
                <div class="flex items-center justify-between relative z-10 w-full">
                    @foreach ($stages as $index => $stage)
                        @php $isPassed = $index <= $currentIdx; @endphp
                        <div class="flex flex-col items-center relative group w-full">
                            @if (!$loop->last)
                                <div class="absolute left-[50%] top-[7px] w-full h-[2px] bg-gray-100 -z-10"></div>
                                @if ($index < $currentIdx)
                                    <div class="absolute left-[50%] top-[7px] w-full h-[2px] bg-black -z-10"></div>
                                @endif
                            @endif
                            <div
                                class="w-4 h-4 rounded-full border-2 z-20 flex-shrink-0 transition-all 
                                {{ $isPassed ? 'bg-black border-black scale-110' : 'bg-white border-gray-300' }}">
                            </div>
                            <div class="mt-4 text-center">
                                <h4
                                    class="text-[10px] font-bold uppercase tracking-wider {{ $isPassed ? 'text-black' : 'text-gray-400' }}">
                                    {{ $stage['label'] }}
                                </h4>
                                @if ($isPassed && $stage['date'])
                                    <p class="text-[9px] text-gray-400 mt-1 font-medium font-mono">
                                        {{ \Carbon\Carbon::parse($stage['date'])->format('d M, H:i') }}
                                    </p>
                                @else
                                    <div class="h-3 mt-1"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        {{-- KOLOM KIRI: PROJECT DETAILS --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- 1. Project Brief --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="file-text" class="w-5 h-5 text-gray-400"></i> Project Brief
                    </h3>
                </div>
                <div class="p-8 space-y-8">
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Project
                            Title</label>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-gray-900 font-bold text-lg">
                            {{ $task->title }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Workspace</label>
                            <div class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 bg-white">
                                <i data-feather="folder" class="w-4 h-4 text-gray-400"></i>
                                <span
                                    class="text-sm font-medium text-gray-700">{{ $task->workspace->name ?? 'Default' }}</span>
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Deadline</label>
                            @php $isUrgent = $task->deadline && now()->diffInDays($task->deadline, false) < 2; @endphp
                            <div
                                class="flex items-center gap-2 p-3 rounded-xl border {{ $isUrgent ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }}">
                                <i data-feather="calendar"
                                    class="w-4 h-4 {{ $isUrgent ? 'text-red-500' : 'text-gray-400' }}"></i>
                                <span class="text-sm font-bold {{ $isUrgent ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ $task->deadline ? $task->deadline->format('d F Y') : 'No Deadline' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            class="text-[10px] font-bold uppercase text-gray-400 tracking-widest block mb-2">Description</label>
                        <div
                            class="prose prose-sm max-w-none text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Attachments --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="paperclip" class="w-5 h-5 text-gray-400"></i> Attachments
                    </h3>
                </div>
                <div class="p-8">
                    @php $files = is_string($task->attachments) ? json_decode($task->attachments, true) : $task->attachments; @endphp
                    @if (!empty($files) && isset($files['path']))
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($files['path']) }}"
                            target="_blank"
                            class="group flex items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-black hover:shadow-md transition-all bg-white w-full md:w-1/2">
                            <div
                                class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-black group-hover:text-white transition-colors border border-gray-200">
                                <i data-feather="file" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $files['original_name'] }}</p>
                                <p class="text-xs text-gray-400">Click to download</p>
                            </div>
                            <i data-feather="download" class="w-4 h-4 text-gray-300 group-hover:text-black"></i>
                        </a>
                    @else
                        <p class="text-sm text-gray-400 italic">No files attached.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: ACTION CENTER --}}
        <div class="space-y-6">

            {{-- 1. PROJECT OWNER --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="relative">
                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $task->user->full_name }}"
                            class="w-12 h-12 rounded-full border border-gray-100 object-cover">
                        @if ($isClientOnline ?? false)
                            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full animate-pulse"
                                title="Online"></div>
                        @else
                            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-gray-300 border-2 border-white rounded-full"
                                title="Offline"></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400 mb-0.5">Project Owner</p>
                        <p class="font-bold text-gray-900 text-sm">{{ $task->user->full_name }}</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            @if ($isClientOnline ?? false)
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
                <a href="{{ route('staff.projects.chat', $task->id) }}"
                    class="w-full py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10 flex items-center justify-center gap-2">
                    <i data-feather="message-circle" class="w-4 h-4"></i>
                    Open Room Chat
                </a>
            </div>

            {{-- 2. DYNAMIC ACTION CARD (Submission Center) --}}
            @if (in_array($task->status, ['in_progress', 'revision']))
                <div
                    class="bg-white rounded-3xl border border-gray-200 shadow-lg shadow-blue-900/5 relative overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-black text-white">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <i data-feather="send" class="w-4 h-4"></i> Submission Center
                        </h3>
                    </div>
                    <div class="p-6">
                        @if ($task->status == 'revision')
                            <div class="bg-orange-50 border border-orange-100 p-3 rounded-xl mb-4 flex gap-2 items-start">
                                <i data-feather="alert-circle" class="w-4 h-4 text-orange-500 mt-0.5"></i>
                                <div class="text-xs text-orange-700">
                                    <strong>Revision Requested!</strong> Please check client feedback and upload the fixed
                                    version.
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('staff.projects.submit', $task->id) }}" method="POST"
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
                                    Submit for Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($task->status == 'active')
                <div class="bg-blue-50 border border-blue-100 rounded-3xl p-8 text-center">
                    <div
                        class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                        <i data-feather="play" class="w-8 h-8 fill-current"></i>
                    </div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Ready to Start?</h3>
                    <p class="text-sm text-blue-700 mb-6">Click the "Start Working" button above to open the submission
                        center.</p>
                </div>
            @elseif($task->status == 'review')
                <div class="bg-purple-50 border border-purple-100 rounded-3xl p-8 text-center">
                    <div
                        class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="eye" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-purple-900 mb-2">Under Review</h3>
                    <p class="text-sm text-purple-700">You have submitted your work. Waiting for feedback.</p>
                </div>
            @elseif($task->status == 'completed')
                <div class="bg-green-50 border border-green-100 rounded-3xl p-8 text-center">
                    <div
                        class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="check-circle" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-green-900 mb-2">Project Completed</h3>
                    <p class="text-sm text-green-700">Great job! This project is officially closed.</p>
                </div>
            @endif

            {{-- 3. SUBMISSION HISTORY --}}
            @if (!in_array($task->status, ['queue', 'active']))
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 fade-in">
                    <h3 class="font-bold text-sm mb-4 flex items-center gap-2">
                        <i data-feather="clock" class="w-4 h-4 text-gray-400"></i> Submission History
                    </h3>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($task->deliverables as $deliv)
                            <div class="relative pl-4 border-l-2 border-gray-100 pb-2">
                                <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full bg-gray-300"></div>
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase">{{ $deliv->created_at->format('d M, H:i') }}</span>
                                        @php
                                            $fileLink =
                                                $deliv->file_type == 'file'
                                                    ? \Illuminate\Support\Facades\Storage::disk('supabase')->url(
                                                        $deliv->file_url,
                                                    )
                                                    : $deliv->file_url;
                                        @endphp
                                        <a href="{{ $fileLink }}" target="_blank"
                                            class="text-[10px] bg-white border border-gray-200 px-2 py-1 rounded-md font-bold hover:bg-black hover:text-white transition-colors">Open</a>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-1 italic">"{{ $deliv->message ?? 'No notes' }}"</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-2">No history yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
