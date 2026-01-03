@extends('client.layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto fade-in h-[calc(100vh-140px)] flex flex-col relative">

        {{-- HEADER --}}
        <div
            class="bg-white px-6 py-4 border-b border-gray-200 flex items-center justify-between rounded-t-3xl shadow-sm z-20">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.support') }}"
                    class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors">
                    <i data-feather="arrow-left" class="w-4 h-4 text-gray-600"></i>
                </a>
                <div>
                    <h1 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        Vektorai Assistant
                        <span
                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-600 uppercase">Beta</span>
                    </h1>
                    <p class="text-xs text-green-600 flex items-center gap-1 font-medium">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        Online • Menjawab Instan
                    </p>
                </div>
            </div>

            {{-- Icon Robot --}}
            <div
                class="w-10 h-10 bg-gradient-to-br from-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i data-feather="cpu" class="w-5 h-5 text-white"></i>
            </div>
        </div>

        {{-- CHAT BODY --}}
        <div class="flex-1 bg-[#F9F8F6] p-6 overflow-y-auto custom-scrollbar relative" id="chat-container">

            {{-- Watermark Background --}}
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                <i data-feather="command" class="w-64 h-64"></i>
            </div>

            {{-- Initial Greeting (Hardcoded for instant load) --}}
            <div class="flex justify-start mb-6 fade-in relative z-10">
                <div class="flex max-w-[85%] gap-3">
                    <div class="flex-shrink-0 mt-1">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                            <i data-feather="cpu" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-bold ml-1 mb-1 block">Vektorai</span>
                        <div
                            class="bg-white border border-gray-200 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm text-sm leading-relaxed">
                            <p>Halo, {{ Auth::user()->full_name ?? 'Kak' }}! 👋</p>
                            <p class="mt-2">Saya <b>Vektorai</b>, asisten pintar Vektora. Saya sudah mempelajari seluruh
                                panduan layanan di sini.</p>
                            <p class="mt-2">Apa yang bisa saya bantu hari ini? Tanyakan tentang harga, revisi, atau cara
                                order!</p>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Barusan</p>
                    </div>
                </div>
            </div>

            {{-- Chat Dynamic Content will be appended here --}}
        </div>

        {{-- INPUT AREA --}}
        <div class="bg-white p-4 border-t border-gray-200 rounded-b-3xl relative z-20">
            <form id="aiChatForm" class="flex items-end gap-3">
                <div
                    class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 focus-within:bg-white focus-within:ring-2 focus-within:ring-purple-100 focus-within:border-purple-300 transition-all shadow-sm">
                    <textarea id="userMessage" rows="1" placeholder="Ketik pertanyaan Anda..."
                        class="w-full bg-transparent p-0 text-sm text-gray-900 placeholder-gray-400 resize-none max-h-32 leading-relaxed outline-none border-none ring-0 focus:ring-0"
                        oninput="autoResize(this)" onkeydown="handleEnter(event)"></textarea>
                </div>
                <button type="submit" id="sendBtn"
                    class="p-3 rounded-xl bg-black text-white hover:bg-gray-800 shadow-lg shadow-black/20 flex-shrink-0 transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i data-feather="send" class="w-5 h-5"></i>
                </button>
            </form>
            <p class="text-center text-[10px] text-gray-400 mt-2">Vektorai bisa membuat kesalahan. Cek kembali info penting.
            </p>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        const chatContainer = document.getElementById('chat-container');
        const form = document.getElementById('aiChatForm');
        const input = document.getElementById('userMessage');
        const sendBtn = document.getElementById('sendBtn');
        const csrfToken = "{{ csrf_token() }}";
        const endpoint = "{{ route('client.support.chat') }}";

        // Auto Resize Textarea
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        // Handle Enter Key to Send
        function handleEnter(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (input.value.trim() !== '') form.dispatchEvent(new Event('submit'));
            }
        }

        // Scroll Bottom
        function scrollToBottom() {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }

        // Form Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            // 1. UI: Disable Input
            input.value = '';
            input.style.height = 'auto';
            input.disabled = true;
            sendBtn.disabled = true;

            // 2. Append User Message
            appendUserMessage(message);
            scrollToBottom();

            // 3. Show Typing Indicator
            const loadingId = appendTypingIndicator();
            scrollToBottom();

            try {
                // 4. Send to Server
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                // Remove Loading
                document.getElementById(loadingId).remove();

                if (data.status === 'success') {
                    appendAiMessage(data.reply);
                } else {
                    appendAiMessage("Maaf, sistem sedang sibuk. Silakan coba lagi nanti.");
                }

            } catch (error) {
                document.getElementById(loadingId).remove();
                appendAiMessage("Terjadi kesalahan koneksi. Periksa internet Anda.");
                console.error(error);
            } finally {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
                scrollToBottom();
            }
        });

        function appendUserMessage(text) {
            const html = `
                <div class="flex justify-end mb-6 fade-in relative z-10">
                    <div class="flex max-w-[85%] gap-3 flex-row-reverse">
                        <div class="flex-shrink-0 mt-1">
                             <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name) }}"
                                class="w-8 h-8 rounded-full bg-gray-200 object-cover border border-gray-200">
                        </div>
                        <div class="text-right">
                             <div class="bg-black text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-md text-sm leading-relaxed text-left">
                                ${escapeHtml(text)}
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 mr-1">You</p>
                        </div>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
        }

        function appendAiMessage(htmlContent) {
            const html = `
                <div class="flex justify-start mb-6 fade-in relative z-10">
                    <div class="flex max-w-[85%] gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center shadow-md">
                                <i data-feather="cpu" class="w-4 h-4 text-white"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 font-bold ml-1 mb-1 block">Vektorai</span>
                            <div class="bg-white border border-gray-200 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm text-sm leading-relaxed">
                                ${htmlContent}
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Barusan</p>
                        </div>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
            feather.replace();
        }

        function appendTypingIndicator() {
            const id = 'loading-' + Date.now();
            const html = `
                <div id="${id}" class="flex justify-start mb-6 fade-in relative z-10">
                    <div class="flex max-w-[85%] gap-3">
                        <div class="flex-shrink-0 mt-1">
                             <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center shadow-md">
                                <i data-feather="cpu" class="w-4 h-4 text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="bg-white border border-gray-200 px-4 py-4 rounded-2xl rounded-tl-sm shadow-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></span>
                                <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                                <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 ml-1 animate-pulse">Sedang mengetik...</p>
                        </div>
                    </div>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', html);
            feather.replace();
            return id;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/\n/g, '<br>');
        }

        // Init Feather Icons
        feather.replace();
    </script>
@endsection
