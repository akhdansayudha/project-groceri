@extends('staff.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        {{-- HEADER CHAT --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-3xl shadow-sm z-10">

            {{-- KIRI: Info Project & Navigation --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="{{ route('staff.projects.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors flex-shrink-0">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>

                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="relative flex-shrink-0">
                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                            class="w-10 h-10 rounded-full border border-gray-100">

                        {{-- Dot Indikator di Avatar Client --}}
                        @if ($isClientOnline)
                            <div
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full animate-pulse">
                            </div>
                        @else
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 border-2 border-white rounded-full">
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-bold text-gray-900 leading-tight truncate">{{ Str::limit($task->title, 30) }}</h1>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="font-mono">#{{ substr($task->id, 0, 8) }}</span>
                            <span class="hidden sm:inline">•</span>
                            <span class="truncate">{{ $task->user->full_name }}</span>
                            @if ($task->status === 'completed')
                                <span
                                    class="bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded text-[9px] uppercase font-bold ml-1">Closed</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: Status Monitor (Client & Admin) --}}
            <div class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                {{-- Status Client --}}
                <div class="flex items-center gap-2" title="Client Status">
                    @if ($isClientOnline)
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-[10px] font-bold text-green-700 uppercase">Client ON</span>
                    @else
                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Client OFF</span>
                    @endif
                </div>

                <div class="w-px h-4 bg-gray-200"></div>

                {{-- Status Admin --}}
                <div class="flex items-center gap-2" title="Admin Status">
                    @if ($isAdminOnline)
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        <span class="text-[10px] font-bold text-blue-700 uppercase">Admin ON</span>
                    @else
                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Admin OFF</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- CHAT BODY --}}
        {{-- UPDATED: Background color changed to bg-[#F9F8F6] --}}
        <div class="flex-1 bg-[#F9F8F6] p-6 overflow-y-auto custom-scrollbar" id="chat-container">

            {{-- Welcome --}}
            <div class="flex justify-center mb-6">
                <div
                    class="bg-white border border-gray-200 px-4 py-2 rounded-full text-[10px] text-gray-400 font-medium shadow-sm uppercase tracking-wide">
                    Project Discussion Room
                </div>
            </div>

            @php
                $lastDate = null;
            @endphp

            @forelse($task->messages as $msg)
                @php
                    // Logic Cek Pengirim (Apakah saya?)
                    $isMe = $msg->sender_id === Auth::id();
                    $senderRole = $msg->user->role ?? 'unknown';

                    // UPDATED: Date Separator Logic
                    $msgDate = $msg->created_at->format('Y-m-d');
                    $showDate = $lastDate !== $msgDate;
                    $lastDate = $msgDate;

                    // Logic Deteksi Pesan Revisi
                    $isRevisionMsg = Str::startsWith($msg->content, 'REVISION REQUESTED:');
                    $cleanContent = str_replace('REVISION REQUESTED:', '', $msg->content);

                    // Styling Label Role
                    $roleBadge = match ($senderRole) {
                        'client' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Client'],
                        'admin' => ['bg' => 'bg-black', 'text' => 'text-white', 'label' => 'Admin'],
                        'staff' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Staff'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'User'],
                    };
                @endphp

                {{-- UPDATED: Render Date Separator if Date Changed --}}
                @if ($showDate)
                    <div class="flex justify-center my-6 fade-in">
                        <span
                            class="bg-gray-200/50 text-gray-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm backdrop-blur-sm">
                            {{ $msg->created_at->isToday() ? 'Today' : $msg->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>
                @endif

                @if ($isRevisionMsg)
                    {{-- TAMPILAN KHUSUS UNTUK REVISION NOTES (Orange Alert) --}}
                    <div class="flex justify-center mb-6 mt-2 fade-in">
                        <div
                            class="bg-orange-50 border border-orange-200 rounded-2xl p-5 max-w-[90%] w-full shadow-sm relative overflow-hidden">
                            {{-- Decorative Bar --}}
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>

                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-orange-100 text-orange-600 rounded-lg shrink-0">
                                    <i data-feather="alert-triangle" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4
                                        class="font-bold text-orange-800 text-sm uppercase tracking-wide mb-1 flex items-center gap-2">
                                        Revision Requested
                                        <span
                                            class="text-[10px] font-normal text-orange-600 lowercase bg-white/50 px-2 py-0.5 rounded-full border border-orange-100">
                                            by {{ $msg->user->full_name }}
                                        </span>
                                    </h4>
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line font-medium">
                                        {{ trim($cleanContent) }}
                                    </p>
                                    <div class="mt-2 flex items-center gap-2 text-[10px] text-orange-400">
                                        <i data-feather="clock" class="w-3 h-3"></i>
                                        {{ $msg->created_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- TAMPILAN CHAT BIASA --}}
                    <div class="flex w-full mb-4 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[85%] md:max-w-[75%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

                            {{-- Avatar Pengirim --}}
                            <div class="flex-shrink-0">
                                <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name) }}"
                                    class="w-8 h-8 rounded-full bg-gray-200 object-cover border border-gray-300">
                            </div>

                            <div>
                                {{-- Nama & Role --}}
                                <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                    @if (!$isMe)
                                        <span
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }}">
                                            {{ $roleBadge['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-bold">
                                            {{ $msg->user->full_name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 font-bold">You</span>
                                    @endif
                                </div>

                                {{-- Bubble Chat --}}
                                <div
                                    class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                                    {{ $isMe
                                        ? 'bg-purple-600 text-white rounded-2xl rounded-tr-sm'
                                        : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                                    @if ($msg->content)
                                        <p>{!! nl2br(e($msg->content)) !!}</p>
                                    @endif

                                    @if ($msg->attachment_url)
                                        <div
                                            class="mt-2 pt-2 border-t {{ $isMe ? 'border-purple-400' : 'border-gray-100' }}">
                                            @php
                                                $link = $msg->attachment_url;
                                                if (!str_starts_with($link, 'http')) {
                                                    $link = \Illuminate\Support\Facades\Storage::disk('supabase')->url(
                                                        $link,
                                                    );
                                                }
                                            @endphp
                                            <a href="{{ $link }}" target="_blank"
                                                class="flex items-center gap-2 group">
                                                <div
                                                    class="w-8 h-8 rounded bg-opacity-20 flex items-center justify-center {{ $isMe ? 'bg-white text-white' : 'bg-gray-100 text-gray-600' }}">
                                                    <i data-feather="file" class="w-4 h-4"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <p
                                                        class="font-bold text-xs truncate max-w-[150px] group-hover:underline">
                                                        Attachment</p>
                                                    <p class="text-[9px] opacity-70">Click to view</p>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <p class="text-[10px] text-gray-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <div class="p-4 bg-gray-100 rounded-full mb-3">
                        <i data-feather="message-square" class="w-6 h-6"></i>
                    </div>
                    <p class="text-sm">No messages yet. Start the discussion!</p>
                </div>
            @endforelse

            <div id="scroll-anchor"></div>
        </div>

        {{-- FOOTER INPUT --}}
        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl relative z-20">
            @if ($task->status === 'completed')
                {{-- TAMPILAN JIKA PROJECT SELESAI --}}
                <div
                    class="flex items-center justify-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                    <i data-feather="lock" class="w-4 h-4"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Obrolan ditutup karena project sudah
                        selesai</span>
                </div>
            @else
                {{-- TAMPILAN FORM CHAT (JIKA AKTIF) --}}
                <form action="{{ route('staff.projects.chat.store', $task->id) }}" method="POST"
                    enctype="multipart/form-data" class="flex items-end gap-3">
                    @csrf

                    {{-- Upload --}}
                    <label
                        class="p-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-black hover:border-black cursor-pointer transition-all flex-shrink-0 group">
                        <input type="file" name="attachment" class="hidden" onchange="checkFile(this)">
                        <i data-feather="paperclip" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </label>

                    {{-- Textarea Wrapper --}}
                    {{-- UPDATED: Removed focus-within:border-black, made it cleaner --}}
                    <div
                        class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus-within:bg-white transition-all shadow-sm">

                        {{-- File Preview --}}
                        <div id="file-preview"
                            class="hidden mb-2 pb-2 border-b border-gray-100 text-xs font-bold text-purple-600 flex items-center gap-1">
                            <i data-feather="file" class="w-3 h-3"></i>
                            <span id="file-name">filename.jpg</span>
                        </div>

                        {{-- Textarea --}}
                        {{-- UPDATED: Added outline-none border-none ring-0 focus:ring-0 --}}
                        <textarea name="message" rows="1" placeholder="Type a message..."
                            class="w-full bg-transparent p-0 text-sm resize-none max-h-32 leading-relaxed outline-none border-none ring-0 focus:ring-0 placeholder-gray-400"
                            oninput="autoResize(this)"></textarea>
                    </div>

                    {{-- Send --}}
                    <button type="submit"
                        class="p-3 rounded-xl bg-purple-600 text-white hover:bg-purple-700 shadow-lg shadow-purple-200 flex-shrink-0 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <i data-feather="send" class="w-5 h-5"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        // Auto scroll to bottom
        window.onload = function() {
            const container = document.getElementById('chat-container');
            if (container) container.scrollTop = container.scrollHeight;
        };

        // Auto resize textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // File preview
        function checkFile(input) {
            const preview = document.getElementById('file-preview');
            const nameSpan = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                preview.classList.remove('hidden');
                nameSpan.innerText = input.files[0].name;
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
@endsection
