@extends('client.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center rounded-t-3xl">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.requests.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>
                <div>
                    <h1 class="font-bold text-lg text-gray-900 leading-tight">{{ $task->title }}</h1>
                    <p class="text-xs text-gray-500">
                        ID: #{{ substr($task->id, 0, 8) }} • {{ $task->service->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-gray-500 uppercase">Live Chat</span>
            </div>
        </div>

        <div class="flex-1 bg-gray-50 p-6 overflow-y-auto space-y-6 custom-scrollbar" id="chat-container">

            {{-- Welcome Message System --}}
            <div class="flex justify-center">
                <div
                    class="bg-white border border-gray-200 px-4 py-2 rounded-full text-xs text-gray-400 font-medium shadow-sm">
                    Project dimulai pada {{ $task->created_at->format('d M Y') }}
                </div>
            </div>

            @forelse($task->messages as $msg)
                @php
                    // Ganti $msg->user_id menjadi $msg->sender_id
                    $isMe = $msg->sender_id === Auth::id();
                @endphp

                <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[80%] md:max-w-[70%] gap-3 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

                        <div class="flex-shrink-0">
                            @if ($isMe)
                                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                    <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name) }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 border border-blue-200">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-end gap-2 {{ $isMe ? 'justify-end' : '' }}">
                                <span class="text-[10px] text-gray-400 font-bold mb-1">
                                    {{ $isMe ? 'You' : $msg->user->full_name ?? 'Staff' }}
                                </span>
                            </div>

                            <div
                                class="relative px-5 py-3 shadow-sm text-sm leading-relaxed
                                {{ $isMe
                                    ? 'bg-black text-white rounded-2xl rounded-tr-sm'
                                    : 'bg-white text-gray-800 border border-gray-200 rounded-2xl rounded-tl-sm' }}">

                                {{-- Pesan Teks: Ganti $msg->message jadi $msg->content --}}
                                @if ($msg->content)
                                    <p>{!! nl2br(e($msg->content)) !!}</p>
                                @endif

                                {{-- Attachment: Ganti $msg->attachments jadi $msg->attachment_url --}}
                                @if ($msg->attachment_url)
                                    <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-gray-700' : 'border-gray-100' }}">
                                        <a href="{{ ($msg->attachment_url) }}"
                                            target="_blank" class="flex items-center gap-2 group">
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

                            <p class="text-[10px] text-gray-400 mt-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse

            {{-- Dummy element untuk auto scroll ke bawah --}}
            <div id="scroll-anchor"></div>
        </div>

        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl">
            <form action="{{ route('client.requests.chat.store', $task->id) }}" method="POST"
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
                    <textarea name="message" rows="1" placeholder="Type your message here..."
                        class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm resize-none max-h-32" oninput="autoResize(this)"></textarea>

                    {{-- File Preview Indicator (Hidden by default) --}}
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

    {{-- Script Auto Scroll & Resize Textarea --}}
    <script>
        // Auto Scroll ke bawah saat load
        window.onload = function() {
            const container = document.getElementById('chat-container');
            container.scrollTop = container.scrollHeight;
        };

        // Auto resize textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // File check logic
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
