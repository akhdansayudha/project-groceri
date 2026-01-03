@extends('staff.layouts.app')

@section('content')
    {{-- LOAD PUSHER & ECHO --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col">

        {{-- HEADER CHAT (TETAP SAMA SEPERTI SEBELUMNYA) --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-3xl shadow-sm z-10">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="{{ route('staff.projects.show', $task->id) }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors flex-shrink-0">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="relative flex-shrink-0">
                        <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                            class="w-10 h-10 rounded-full border border-gray-100">
                        {{-- Dot Indikator --}}
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Monitor --}}
            <div class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                <div class="flex items-center gap-2">
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
                <div class="flex items-center gap-2">
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
        <div class="flex-1 bg-[#F9F8F6] p-6 overflow-y-auto custom-scrollbar" id="chat-container">
            <div class="flex justify-center mb-6">
                <div
                    class="bg-white border border-gray-200 px-4 py-2 rounded-full text-[10px] text-gray-400 font-medium shadow-sm uppercase tracking-wide">
                    Staff Workspace
                </div>
            </div>

            @php $lastDate = null; @endphp
            @foreach ($task->messages as $msg)
                @php
                    $isMe = $msg->sender_id === Auth::id();
                    $msgDate = $msg->created_at->format('Y-m-d');
                    $showDate = $lastDate !== $msgDate;
                    $lastDate = $msgDate;
                @endphp

                @if ($showDate)
                    <div class="flex justify-center my-6 fade-in">
                        <span
                            class="bg-gray-200/50 text-gray-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm backdrop-blur-sm">
                            {{ $msg->created_at->isToday() ? 'Today' : $msg->created_at->translatedFormat('d F Y') }}
                        </span>
                    </div>
                @endif

                @include('staff.projects.partials.chat-bubble', ['msg' => $msg, 'isMe' => $isMe])
            @endforeach
        </div>

        {{-- FOOTER INPUT --}}
        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl relative z-20">
            @if ($task->status === 'completed')
                <div
                    class="flex items-center justify-center gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                    <i data-feather="lock" class="w-4 h-4"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Project Completed</span>
                </div>
            @else
                <form id="chatForm" onsubmit="sendMessage(event)" enctype="multipart/form-data"
                    class="flex items-end gap-3">

                    {{-- Upload --}}
                    <label
                        class="p-3 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-purple-600 hover:border-purple-200 cursor-pointer transition-all flex-shrink-0 group">
                        <input type="file" name="attachment" class="hidden" onchange="checkFile(this)">
                        <i data-feather="paperclip" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </label>

                    {{-- Textarea --}}
                    <div
                        class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus-within:bg-white transition-all shadow-sm">
                        <div id="file-preview"
                            class="hidden mb-2 pb-2 border-b border-gray-100 text-xs font-bold text-purple-600 flex items-center gap-2">
                            <i data-feather="file" class="w-3 h-3"></i>
                            <span id="file-name">filename.jpg</span>
                            <button type="button" onclick="clearFile()" class="ml-auto text-gray-400 hover:text-red-500">
                                <i data-feather="x" class="w-3 h-3"></i>
                            </button>
                        </div>

                        <textarea name="message" id="messageInput" rows="1" placeholder="Type a message..."
                            class="w-full bg-transparent p-0 text-sm resize-none max-h-32 leading-relaxed outline-none border-none ring-0 focus:ring-0 placeholder-gray-400"
                            oninput="handleInput(this)"></textarea>
                    </div>

                    {{-- Send --}}
                    <button type="submit" id="submitBtn"
                        class="p-3 rounded-xl bg-purple-600 text-white hover:bg-purple-700 shadow-lg shadow-purple-200 flex-shrink-0 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <i data-feather="send" class="w-5 h-5"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        const taskId = "{{ $task->id }}";
        const container = document.getElementById('chat-container');
        const form = document.getElementById('chatForm');
        let typingTimer;

        // 1. SETUP LARAVEL ECHO
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        // 2. SUBSCRIBE CHANNEL
        const channel = window.Echo.private('chat.' + taskId);

        channel
            .listen('.message.sent', (e) => { // Perhatikan titik (.)
                removeTypingIndicator();
                container.insertAdjacentHTML('beforeend', e.html);
                scrollToBottom();
                feather.replace();
            })
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

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>';

            try {
                // PENTING: Ambil Socket ID untuk mencegah double bubble
                const socketId = window.Echo.socketId();

                const response = await fetch("{{ route('staff.projects.chat.store', $task->id) }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'X-Socket-ID': socketId // <--- WAJIB ADA
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    container.insertAdjacentHTML('beforeend', data.html);
                    scrollToBottom();
                    feather.replace();

                    form.reset();
                    clearFile();
                    const textarea = document.getElementById('messageInput');
                    textarea.style.height = 'auto';
                } else {
                    alert('Failed: ' + (data.message || 'Error sending message'));
                }
            } catch (error) {
                console.error(error);
                alert('Connection error.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
                feather.replace();
            }
        }

        // 4. TYPING LOGIC
        function handleInput(textarea) {
            autoResize(textarea);
            channel.whisper('typing', {
                name: "Staff"
            });
        }

        function showTypingIndicator(name) {
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

            clearTimeout(typingTimer);
            typingTimer = setTimeout(removeTypingIndicator, 3000);
        }

        function removeTypingIndicator() {
            const el = document.getElementById('typing-indicator');
            if (el) el.remove();
        }

        // UI HELPERS
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

        window.onload = scrollToBottom;
    </script>
@endsection
