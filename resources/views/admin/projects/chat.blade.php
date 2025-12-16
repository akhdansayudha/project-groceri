@extends('admin.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        {{-- HEADER CHAT --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-3xl shadow-sm z-10">

            {{-- KIRI: Navigasi & Info Project --}}
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="{{ route('admin.projects.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors flex-shrink-0">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>

                <div class="min-w-0">
                    <h1 class="font-bold text-gray-900 leading-tight truncate">{{ $task->title }}</h1>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                        <span class="font-mono">#{{ substr($task->id, 0, 8) }}</span>
                        <span>•</span>
                        <span
                            class="bg-gray-100 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">{{ $task->service->name }}</span>
                        @if ($task->status === 'completed')
                            <span
                                class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-[9px] uppercase font-bold">Completed</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KANAN: Monitor Status (Client & Staff) - Desain Staff Style --}}
            <div
                class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-full border border-gray-100 shadow-sm whitespace-nowrap">

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

                {{-- Status Staff --}}
                <div class="flex items-center gap-2" title="Staff Status">
                    @if ($isStaffOnline)
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-600"></span>
                        </span>
                        <span class="text-[10px] font-bold text-purple-700 uppercase">Staff ON</span>
                    @else
                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Staff OFF</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- CHAT BODY --}}
        <div class="flex-1 bg-gray-50 p-6 overflow-y-auto space-y-6 custom-scrollbar" id="chat-container">

            {{-- Welcome Message System --}}
            <div class="flex justify-center">
                <div
                    class="bg-white border border-gray-200 px-4 py-2 rounded-full text-[10px] text-gray-400 font-medium shadow-sm uppercase tracking-wide">
                    Admin Supervision Room
                </div>
            </div>

            @forelse($task->messages as $msg)
                @php
                    // Cek apakah pesan dari user yang sedang login (Admin)
                    $isMe = $msg->sender_id === Auth::id();
                    $senderRole = $msg->user->role ?? 'unknown';

                    // Cek Pesan Revisi
                    $isRevisionMsg = \Illuminate\Support\Str::startsWith($msg->content, 'REVISION REQUESTED');
                    // Bersihkan prefix text untuk tampilan
                    $cleanContent = str_replace(
                        ['REVISION REQUESTED (BY ADMIN):', 'REVISION REQUESTED:'],
                        '',
                        $msg->content,
                    );

                    // Warna Label Role
                    $roleBadge = match ($senderRole) {
                        'client' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Client'],
                        'staff' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Staff'],
                        'admin' => ['bg' => 'bg-black', 'text' => 'text-white', 'label' => 'Admin'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => 'User'],
                    };
                @endphp

                @if ($isRevisionMsg)
                    {{-- TAMPILAN KHUSUS REVISI (Orange Box) --}}
                    <div class="flex justify-center mb-6 mt-2 fade-in w-full">
                        <div
                            class="bg-orange-50 border border-orange-200 rounded-2xl p-5 max-w-[85%] w-full shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-orange-500"></div>

                            <div class="flex items-start gap-4">
                                <div class="p-2.5 bg-orange-100 text-orange-600 rounded-xl shrink-0">
                                    <i data-feather="alert-triangle" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4
                                            class="font-bold text-orange-900 text-sm uppercase tracking-wide flex items-center gap-2">
                                            Revision Requested
                                            <span
                                                class="text-[10px] font-bold text-orange-600 bg-white/60 px-2 py-0.5 rounded-full border border-orange-100 normal-case">
                                                by {{ $msg->user->full_name }}
                                            </span>
                                        </h4>
                                        <span
                                            class="text-[10px] text-orange-400 font-mono">{{ $msg->created_at->format('d M, H:i') }}</span>
                                    </div>
                                    <p
                                        class="text-sm text-gray-800 leading-relaxed whitespace-pre-line font-medium border-t border-orange-200/50 pt-2 mt-1">
                                        {{ trim($cleanContent) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- TAMPILAN CHAT BIASA --}}
                    <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[85%] md:max-w-[75%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

                            {{-- Avatar --}}
                            <div class="flex-shrink-0">
                                <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name ?? 'User') }}"
                                    class="w-9 h-9 rounded-full bg-gray-200 object-cover border border-gray-300 shadow-sm">
                            </div>

                            <div>
                                {{-- Nama & Role Badge --}}
                                <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                    @if (!$isMe)
                                        <span
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }}">
                                            {{ $roleBadge['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-bold">
                                            {{ $msg->user->full_name ?? 'Unknown' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 font-bold">You (Admin)</span>
                                    @endif
                                </div>

                                {{-- Bubble --}}
                                <div
                                    class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                                    {{ $isMe
                                        ? 'bg-black text-white rounded-2xl rounded-tr-sm'
                                        : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                                    @if ($msg->content)
                                        <p>{!! nl2br(e($msg->content)) !!}</p>
                                    @endif

                                    {{-- Attachment --}}
                                    @if ($msg->attachment_url)
                                        <div
                                            class="mt-2 pt-2 border-t {{ $isMe ? 'border-gray-700' : 'border-gray-100' }}">
                                            @php
                                                $link = $msg->attachment_url;
                                                if (!str_starts_with($link, 'http')) {
                                                    $link = \Illuminate\Support\Facades\Storage::disk('supabase')->url(
                                                        $link,
                                                    );
                                                }
                                            @endphp
                                            <a href="{{ $link }}" target="_blank"
                                                class="flex items-center gap-3 group p-1 rounded hover:bg-white/10 transition-colors">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-opacity-20 flex items-center justify-center {{ $isMe ? 'bg-white text-white' : 'bg-gray-100 text-gray-600' }}">
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
                    <p class="text-sm">Belum ada percakapan di project ini.</p>
                </div>
            @endforelse

            <div id="scroll-anchor"></div>
        </div>

        {{-- FOOTER INPUT --}}
        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl">
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
                <form action="{{ route('admin.projects.chat.store', $task->id) }}" method="POST"
                    enctype="multipart/form-data" class="flex items-end gap-3">
                    @csrf

                    {{-- Upload Button --}}
                    <label
                        class="p-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-black hover:border-black cursor-pointer transition-all flex-shrink-0">
                        <input type="file" name="attachment" class="hidden" onchange="checkFile(this)">
                        <i data-feather="paperclip" class="w-5 h-5"></i>
                    </label>

                    {{-- Text Area --}}
                    <div
                        class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus-within:border-black focus-within:bg-white transition-all">
                        <textarea name="message" rows="1" placeholder="Type message as Admin..."
                            class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm resize-none max-h-32" oninput="autoResize(this)"></textarea>

                        <div id="file-preview" class="hidden mt-2 text-xs font-bold text-blue-600 flex items-center gap-1">
                            <i data-feather="file" class="w-3 h-3"></i>
                            <span id="file-name">filename.jpg</span>
                        </div>
                    </div>

                    {{-- Send Button --}}
                    <button type="submit"
                        class="p-3 rounded-xl bg-black text-white hover:bg-gray-800 shadow-lg shadow-black/20 flex-shrink-0 transition-all">
                        <i data-feather="send" class="w-5 h-5"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        window.onload = function() {
            const container = document.getElementById('chat-container');
            if (container) container.scrollTop = container.scrollHeight;
        };

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

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
