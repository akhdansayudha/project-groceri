@extends('client.layouts.app')

@section('content')
    <div class="fade-in pb-20">

        {{-- HERO SECTION --}}
        <div class="text-center mb-12 py-10">
            <h1 class="text-4xl font-bold tracking-tight mb-4 text-gray-900">How can we help you?</h1>
            <p class="text-gray-500 mb-8 max-w-2xl mx-auto text-lg">
                Temukan jawaban seputar layanan, membership tier, billing, dan teknis pengerjaan project di sini.
            </p>

            {{-- SEARCH BAR --}}
            <div class="relative max-w-xl mx-auto z-50">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari pertanyaan... (misal: top up, revisi, tier)"
                        class="w-full bg-white border border-gray-200 rounded-2xl py-4 pl-12 pr-6 shadow-xl shadow-black/5 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-all text-sm font-medium"
                        autocomplete="off">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                </div>

                {{-- SEARCH SUGGESTIONS DROPDOWN --}}
                <div id="searchSuggestions"
                    class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 hidden overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                    {{-- Hasil pencarian akan muncul di sini via JS --}}
                </div>
            </div>
        </div>

        {{-- CONTACT CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16 max-w-5xl mx-auto">
            <a href="https://wa.me/6281234567890" target="_blank"
                class="group bg-[#25D366] text-white p-8 rounded-3xl shadow-xl shadow-green-500/20 hover:shadow-2xl hover:-translate-y-1 transition-all relative overflow-hidden">
                <div class="absolute right-0 top-0 p-6 opacity-10">
                    <i data-feather="message-circle" class="w-32 h-32 transform rotate-12"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                            <i data-feather="message-circle" class="w-6 h-6 text-white"></i>
                        </div>
                        <i data-feather="arrow-up-right"
                            class="w-6 h-6 opacity-70 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </div>
                    <h3 class="text-2xl font-bold">WhatsApp Support</h3>
                    <p class="text-green-50 text-sm mt-2 opacity-90 font-medium">Respon cepat (09:00 - 17:00 WIB)</p>
                </div>
            </a>

            <a href="mailto:support@vektora.id"
                class="group bg-white border border-gray-200 p-8 rounded-3xl shadow-sm hover:border-black hover:shadow-xl transition-all relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div
                            class="p-3 bg-gray-100 rounded-2xl text-black group-hover:bg-black group-hover:text-white transition-colors">
                            <i data-feather="mail" class="w-6 h-6"></i>
                        </div>
                        <i data-feather="arrow-up-right"
                            class="w-6 h-6 text-gray-400 group-hover:text-black transition-colors"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Email Support</h3>
                    <p class="text-gray-500 text-sm mt-2 font-medium">Untuk kendala teknis & billing complex</p>
                </div>
            </a>
        </div>

        {{-- FAQ SECTION --}}
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-center mb-8">Frequently Asked Questions</h2>

            @foreach ($faqs as $category => $questions)
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-5 border-b border-gray-100 pb-2">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            @if ($category == 'Membership & Tiers')
                                <i data-feather="star" class="w-4 h-4 text-gray-600"></i>
                            @elseif($category == 'Billing & Token')
                                <i data-feather="credit-card" class="w-4 h-4 text-gray-600"></i>
                            @else
                                <i data-feather="layers" class="w-4 h-4 text-gray-600"></i>
                            @endif
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">{{ $category }}</h3>
                    </div>

                    <div class="space-y-3">
                        @foreach ($questions as $faq)
                            <div id="{{ $faq['id'] }}"
                                class="border border-gray-200 rounded-2xl bg-white overflow-hidden faq-item transition-all duration-300 hover:border-gray-300">
                                <button onclick="toggleAccordion('{{ $faq['id'] }}')"
                                    class="w-full text-left px-6 py-5 flex justify-between items-center focus:outline-none bg-white">
                                    <span class="font-bold text-sm text-gray-800 faq-question">{{ $faq['question'] }}</span>
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center transition-transform duration-300 icon-wrapper">
                                        <i data-feather="plus" class="w-4 h-4 text-gray-500 icon-plus"></i>
                                    </div>
                                </button>
                                <div id="answer-{{ $faq['id'] }}"
                                    class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                                    <div class="px-6 pb-6 text-sm text-gray-500 leading-loose border-t border-gray-50 pt-4">
                                        {!! $faq['answer'] !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        // --- 1. ACCORDION LOGIC (Exclusive Open) ---
        function toggleAccordion(id) {
            const content = document.getElementById('answer-' + id);
            const parent = document.getElementById(id);
            const iconPlus = parent.querySelector('.icon-plus');
            const iconWrapper = parent.querySelector('.icon-wrapper');

            // Cek apakah sedang terbuka
            const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

            // TUTUP SEMUA FAQ LAINNYA
            document.querySelectorAll('.faq-item').forEach(item => {
                const ans = item.querySelector('[id^="answer-"]');
                const icn = item.querySelector('.icon-plus');
                const wrap = item.querySelector('.icon-wrapper');

                ans.style.maxHeight = '0px';
                item.classList.remove('border-black', 'shadow-md');
                item.classList.add('border-gray-200');

                // Reset Icon
                icn.innerHTML = feather.icons['plus'].toSvg({
                    class: 'w-4 h-4 text-gray-500'
                });
                wrap.classList.remove('bg-black', 'text-white');
                wrap.classList.add('bg-gray-50');
            });

            // LOGIKA BUKA / TUTUP KLIK SAAT INI
            if (!isOpen) {
                content.style.maxHeight = content.scrollHeight + "px";
                parent.classList.remove('border-gray-200');
                parent.classList.add('border-black', 'shadow-md');

                // Ubah Icon jadi Minus & Hitam
                iconWrapper.classList.remove('bg-gray-50');
                iconWrapper.classList.add('bg-black', 'text-white');
                iconPlus.innerHTML = feather.icons['minus'].toSvg({
                    class: 'w-4 h-4 text-white'
                });
            }
        }

        // --- 2. SEARCH SUGGESTION LOGIC ---
        const searchInput = document.getElementById('searchInput');
        const suggestionsBox = document.getElementById('searchSuggestions');
        const allQuestions = document.querySelectorAll('.faq-question');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            suggestionsBox.innerHTML = '';

            if (query.length < 2) {
                suggestionsBox.classList.add('hidden');
                return;
            }

            let hasResult = false;

            allQuestions.forEach(q => {
                const text = q.innerText;
                if (text.toLowerCase().includes(query)) {
                    hasResult = true;
                    // Ambil ID parent element
                    const parentId = q.closest('.faq-item').id;

                    // Buat Item Suggestion
                    const div = document.createElement('div');
                    div.className =
                        'px-5 py-3 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-50 last:border-0 flex items-center gap-3';
                    div.innerHTML = `
                        <i data-feather="search" class="w-3 h-3 text-gray-400"></i>
                        <span class="text-gray-700 truncate">${text}</span>
                    `;

                    // Event Klik Suggestion
                    div.onclick = () => {
                        // Scroll ke elemen
                        const target = document.getElementById(parentId);
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Buka Accordion
                        setTimeout(() => {
                            // Cek jika belum terbuka, maka buka
                            const content = document.getElementById('answer-' + parentId);
                            if (!content.style.maxHeight || content.style.maxHeight === '0px') {
                                toggleAccordion(parentId);
                            }
                        }, 500);

                        // Reset Search
                        suggestionsBox.classList.add('hidden');
                        searchInput.value = '';
                    };

                    suggestionsBox.appendChild(div);
                }
            });

            if (hasResult) {
                suggestionsBox.classList.remove('hidden');
                feather.replace(); // Refresh icon di dalam suggestion
            } else {
                suggestionsBox.innerHTML =
                    '<div class="px-5 py-4 text-sm text-gray-400 text-center">Tidak ada hasil ditemukan.</div>';
                suggestionsBox.classList.remove('hidden');
            }
        });

        // Klik di luar untuk menutup suggestion
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.add('hidden');
            }
        });
    </script>

    {{-- TAWK.TO LIVE CHAT --}}
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/693d9f840981ca197f7a92cb/1jccbe7k7';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
@endsection
