@extends('client.layouts.app')

@section('content')
    {{-- LOAD PUSHER & ECHO --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        {{-- HEADER (Tetap Sama) --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-3xl shadow-sm z-20 relative">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="{{ route('client.requests.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors flex-shrink-0">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>
                <div class="min-w-0">
                    <h1 class="font-bold text-lg text-gray-900 leading-tight truncate">{{ $task->title }}</h1>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span
                            class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">#{{ substr($task->id, 0, 8) }}</span>
                        <span class="hidden sm:inline">•</span>
                        <span class="truncate">{{ $task->service->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Status Monitor --}}
            <div class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                {{-- Status Staff --}}
                <div class="flex items-center gap-2" title="Assigned Staff Status">
                    @if ($isStaffOnline ?? false)
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-[10px] font-bold text-green-700 uppercase">Staff ON</span>
                    @else
                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Staff OFF</span>
                    @endif
                </div>
                {{-- Separator --}}
                <div class="w-px h-4 bg-gray-200"></div>
                {{-- Status Admin --}}
                <div class="flex items-center gap-2" title="Admin Status">
                    @if ($isAdminOnline ?? false)
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
        <div class="flex-1 bg-[#F9F8F6] p-6 overflow-y-auto custom-scrollbar" id="chat-container">
            {{-- Welcome Message --}}
            <div class="flex justify-center mb-6">
                <div
                    class="bg-white border border-gray-200 px-4 py-1.5 rounded-full text-[10px] text-gray-400 font-medium shadow-sm uppercase tracking-wide">
                    Project Started: {{ $task->created_at->format('d M Y') }}
                </div>
            </div>

            {{-- Load Pesan Lama (SSR) --}}
            @foreach ($task->messages as $msg)
                @php
                    $isMe = $msg->sender_id === Auth::id();
                    // Include Partial Chat Bubble
                @endphp
                @include('client.requests.partials.chat-bubble', ['msg' => $msg, 'isMe' => $isMe])
            @endforeach
        </div>

        {{-- FOOTER INPUT --}}
        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl relative z-20">
            @if ($task->status === 'completed')
                <div
                    class="flex items-center justify-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                    <i data-feather="lock" class="w-4 h-4"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Obrolan ditutup karena project sudah
                        selesai</span>
                </div>
            @else
                {{-- Form Chat (AJAX) --}}
                <form id="chatForm" onsubmit="sendMessage(event)" enctype="multipart/form-data"
                    class="flex items-end gap-3">

                    {{-- Upload Button --}}
                    <label
                        class="p-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-black hover:border-gray-300 cursor-pointer transition-all flex-shrink-0 group">
                        <input type="file" name="attachment" class="hidden" onchange="checkFile(this)">
                        <i data-feather="paperclip" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </label>

                    {{-- Text Area --}}
                    <div
                        class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 focus-within:bg-white transition-all shadow-sm">
                        {{-- File Preview --}}
                        <div id="file-preview"
                            class="hidden mb-2 pb-2 border-b border-gray-100 text-xs font-bold text-blue-600 flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center">
                                <i data-feather="file" class="w-3 h-3"></i>
                            </div>
                            <span id="file-name" class="truncate max-w-[200px]">filename.jpg</span>
                            <button type="button" onclick="clearFile()" class="ml-auto text-gray-400 hover:text-red-500">
                                <i data-feather="x" class="w-3 h-3"></i>
                            </button>
                        </div>

                        {{-- Input Text --}}
                        <textarea name="message" id="messageInput" rows="1" placeholder="Ketik pesan Anda..."
                            class="w-full bg-transparent p-0 text-sm text-gray-900 placeholder-gray-400 resize-none max-h-32 leading-relaxed outline-none border-none ring-0 focus:ring-0"
                            oninput="handleInput(this)"></textarea>
                    </div>

                    {{-- Send Button --}}
                    <button type="submit" id="submitBtn"
                        class="p-3 rounded-xl bg-black text-white hover:bg-gray-800 shadow-lg shadow-black/20 flex-shrink-0 transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-feather="send" class="w-5 h-5"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        const taskId = "{{ $task->id }}";
        const userId = "{{ Auth::id() }}";
        const container = document.getElementById('chat-container');
        const form = document.getElementById('chatForm');
        let typingTimer;

        // 1. SETUP LARAVEL ECHO (REALTIME)
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        // 2. SUBSCRIBE KE CHANNEL CHAT
        const channel = window.Echo.private('chat.' + taskId);

        channel
            // A. DENGARKAN PESAN MASUK
            .listen('.message.sent', (e) => {
                // Hapus indikator typing jika ada
                removeTypingIndicator();
                // Append HTML yang dikirim server
                container.insertAdjacentHTML('beforeend', e.html);
                scrollToBottom();
                feather.replace(); // Refresh icon
            })
            // B. DENGARKAN TYPING INDICATOR (Whisper)
            .listenForWhisper('typing', (e) => {
                showTypingIndicator(e.name);
            });

        // 3. SEND MESSAGE VIA AJAX
        async function sendMessage(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const message = formData.get('message');
            const attachment = formData.get('attachment');

            if (!message && (!attachment || attachment.size === 0)) return;

            const submitBtn = document.getElementById('submitBtn');
            const originalBtnContent = submitBtn.innerHTML;

            // UI Loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>';

            try {
                const socketId = window.Echo.socketId();

                const response = await fetch("{{ route('client.requests.chat.store', $task->id) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'X-Socket-ID': socketId
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    // Tambahkan pesan sendiri (rendered dari server)
                    container.insertAdjacentHTML('beforeend', data.html);
                    scrollToBottom();
                    feather.replace();

                    // Reset Form
                    form.reset();
                    clearFile();
                    const textarea = document.getElementById('messageInput');
                    textarea.style.height = 'auto'; // Reset height
                } else {
                    alert('Gagal mengirim pesan: ' + (data.message || 'Error'));
                }
            } catch (error) {
                console.error(error);
                alert('Koneksi bermasalah.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
                feather.replace();
            }
        }

        // 4. TYPING INDICATOR LOGIC
        function handleInput(textarea) {
            autoResize(textarea); // Resize tinggi textarea

            // Kirim event whisper 'typing'
            channel.whisper('typing', {
                name: "{{ Auth::user()->full_name }}"
            });
        }

        function showTypingIndicator(name) {
            // Cek jika indikator sudah ada
            if (document.getElementById('typing-indicator')) return;

            const html = `
                <div id="typing-indicator" class="flex items-center gap-2 mb-4 ml-4 fade-in transition-all">
                    <div class="flex gap-1 bg-gray-100 p-3 rounded-2xl rounded-tl-none border border-gray-200">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    </div>
                    <span class="text-[10px] text-gray-400 italic">${name} is typing...</span>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            scrollToBottom();

            // Reset timer untuk menghilangkan indikator
            clearTimeout(typingTimer);
            typingTimer = setTimeout(removeTypingIndicator, 3000);
        }

        function removeTypingIndicator() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        // Helpers UI
        function scrollToBottom() {
            if (container) container.scrollTop = container.scrollHeight;
        }

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

        function clearFile() {
            const input = document.querySelector('input[name="attachment"]');
            input.value = '';
            checkFile(input);
        }

        // Init Scroll
        window.onload = scrollToBottom;
    </script>
@endsection
