@extends('admin.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        {{-- HEADER CHAT --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center rounded-t-3xl shadow-sm z-10">
            <div class="flex items-center gap-4">
                {{-- FIX: Tombol Back ke Admin Project Show --}}
                <a href="{{ route('admin.projects.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>

                {{-- Info Project & Client --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                            class="w-10 h-10 rounded-full border border-gray-100">

                        {{-- BADGE ONLINE / OFFLINE (Client) --}}
                        @if ($isClientOnline)
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full animate-pulse"
                                title="Client Online"></div>
                        @else
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 border-2 border-white rounded-full"
                                title="Client Offline"></div>
                        @endif
                    </div>
                    <div>
                        <h1 class="font-bold text-gray-900 leading-tight">{{ $task->title }}</h1>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>#{{ substr($task->id, 0, 8) }}</span>
                            <span>•</span>
                            <span>{{ $task->user->full_name }} (Client)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Indikator Text --}}
            <div class="flex items-center gap-2">
                @if ($isClientOnline)
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-xs font-bold text-green-600 uppercase">Client Online</span>
                @else
                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                    <span class="text-xs font-bold text-gray-400 uppercase">Client Offline</span>
                @endif
            </div>
        </div>

        {{-- CHAT BODY --}}
        <div class="flex-1 bg-gray-50 p-6 overflow-y-auto space-y-6 custom-scrollbar" id="chat-container">

            {{-- Welcome Message System --}}
            <div class="flex justify-center">
                <div
                    class="bg-white border border-gray-200 px-4 py-2 rounded-full text-[10px] text-gray-400 font-medium shadow-sm uppercase tracking-wide">
                    Room Chat: Admin, Staff, & Client
                </div>
            </div>

            @forelse($task->messages as $msg)
                @php
                    // Cek apakah pesan dari user yang sedang login (Admin)
                    $isMe = $msg->sender_id === Auth::id();

                    // Cek Role Pengirim untuk Label
                    $senderRole = $msg->user->role ?? 'unknown'; // client, staff, admin

                    // Tentukan Warna Label Role
                    $roleBadgeClass = match ($senderRole) {
                        'client' => 'bg-blue-100 text-blue-700',
                        'staff' => 'bg-purple-100 text-purple-700',
                        'admin' => 'bg-black text-white',
                        default => 'bg-gray-100 text-gray-600',
                    };
                @endphp

                <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[85%] md:max-w-[75%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

                        {{-- AVATAR PENGIRIM --}}
                        <div class="flex-shrink-0">
                            <img src="{{ $msg->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->full_name ?? 'User') }}"
                                class="w-8 h-8 rounded-full bg-gray-200 object-cover border border-gray-300">
                        </div>

                        <div>
                            {{-- NAMA PENGIRIM & LABEL ROLE --}}
                            <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                @if (!$isMe)
                                    <span
                                        class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase {{ $roleBadgeClass }}">
                                        {{ $senderRole }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-bold">
                                        {{ $msg->user->full_name ?? 'Unknown' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 font-bold">You (Admin)</span>
                                @endif
                            </div>

                            {{-- ISI PESAN --}}
                            <div
                                class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                                {{ $isMe
                                    ? 'bg-black text-white rounded-2xl rounded-tr-sm'
                                    : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                                {{-- Text Content --}}
                                @if ($msg->content)
                                    <p>{!! nl2br(e($msg->content)) !!}</p>
                                @endif

                                {{-- Attachment --}}
                                @if ($msg->attachment_url)
                                    <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-gray-700' : 'border-gray-100' }}">

                                        {{-- LOGIC FIX URL GANDA --}}
                                        @php
                                            $attachmentLink = $msg->attachment_url;
                                            // Jika di database belum ada 'http', baru kita bungkus pakai Storage::url
                                            if (!str_starts_with($attachmentLink, 'http')) {
                                                $attachmentLink = \Illuminate\Support\Facades\Storage::disk(
                                                    'supabase',
                                                )->url($attachmentLink);
                                            }
                                        @endphp

                                        <a href="{{ $attachmentLink }}" target="_blank"
                                            class="flex items-center gap-2 group">
                                            <div
                                                class="w-8 h-8 rounded bg-opacity-20 flex items-center justify-center {{ $isMe ? 'bg-white text-white' : 'bg-gray-100 text-gray-600' }}">
                                                <i data-feather="file" class="w-4 h-4"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="font-bold text-xs truncate max-w-[150px] group-hover:underline">
                                                    Attachment
                                                </p>
                                                <p class="text-[9px] opacity-70">Click to view</p>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- WAKTU PESAN --}}
                            <p class="text-[10px] text-gray-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
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
            {{-- Form Action ke Admin Chat Store --}}
            <form action="{{ route('admin.projects.chat.store', $task->id) }}" method="POST" enctype="multipart/form-data"
                class="flex items-end gap-3">
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
