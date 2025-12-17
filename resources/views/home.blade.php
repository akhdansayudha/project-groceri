@extends('layouts.app')

@section('content')
    <section class="h-screen w-full flex flex-col justify-center items-center relative overflow-hidden bg-white">

        <div class="text-center max-w-6xl mx-auto z-10 -mt-10 px-6">
            <h1 class="reveal text-6xl md:text-8xl lg:text-9xl font-bold leading-[1] mb-8 tracking-tighter">
                Your trusted <br> creative partner.
            </h1>
            <p class="reveal text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                We deliver creative branding, web design, and UI/UX solutions to make the most impact for your business.
            </p>
            <div class="reveal flex gap-4 justify-center">
                <button class="px-8 py-4 rounded-full font-bold btn-invert btn-black hover-target flex items-center gap-2">
                    Request a quote <span>👋</span>
                </button>
            </div>
        </div>

        <div class="marquee-container hover-target">
            <div class="marquee-content text-4xl md:text-5xl font-light text-gray-300 uppercase tracking-tighter">
                <span class="mx-8">/ STRATEGIC EXPERIENCES</span>
                <span class="mx-8 text-black font-medium">/ RESULTS DRIVEN SOLUTIONS</span>
                <span class="mx-8">/ BUSINESS VALUE</span>
                <span class="mx-8 text-black font-medium">/ PURPOSEFUL DESIGNS</span>
                <span class="mx-8">/ STRATEGIC EXPERIENCES</span>
                <span class="mx-8 text-black font-medium">/ RESULTS DRIVEN SOLUTIONS</span>
                <span class="mx-8">/ BUSINESS VALUE</span>
                <span class="mx-8 text-black font-medium">/ PURPOSEFUL DESIGNS</span>
            </div>
        </div>
    </section>

    <section id="who-we-are" class="py-32 px-6 bg-[#F3F4F6]">
        <div class="max-w-7xl mx-auto">
            <div class="mb-12 flex items-center gap-3">
                <span class="w-2 h-2 bg-black rounded-full"></span>
                <h3 class="text-sm font-bold uppercase tracking-widest">Who we are</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div
                    class="reveal rounded-[2rem] aspect-square md:aspect-video relative group hover-target overflow-hidden">
                    <img src="https://azgwfpkdujdvpfxnnieb.supabase.co/storage/v1/object/public/chat-attachments/landingpage/42950.jpg"
                        alt="Vektora Team"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors duration-500"></div>
                </div>

                <div class="reveal">
                    <h2 class="reveal text-4xl md:text-6xl font-bold mb-8 leading-[1.1] tracking-tight">We are design-first
                        creative studio</h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-10">
                        We believe in the power of purposeful design to solve real business challenges. Every line, color,
                        and interaction is crafted with intent, creating experiences that connect and drive impact.
                    </p>
                    <a href="#"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-black text-white rounded-full font-bold btn-invert btn-black hover-target">
                        About us <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="featured" class="py-32 px-6 bg-white rounded-t-[3rem]">
        <div class="max-w-7xl mx-auto">
            <div class="mb-24">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-2 h-2 bg-black rounded-full"></span>
                    <h3 class="text-sm font-bold uppercase tracking-widest">Featured Works</h3>
                </div>
                <h2 class="reveal text-5xl md:text-7xl font-bold reveal tracking-tight">We create solutions but most <br>
                    importantly we identify problems.</h2>
            </div>

            <div class="reveal reveal-zoom mb-32 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center group">
                <div class="lg:col-span-4">
                    <div class="mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-black rounded-full"></span>
                        <span class="text-sm font-bold">Skincare Brand, US</span>
                    </div>
                    <h3
                        class="text-5xl font-bold mb-6 group-hover:underline decoration-2 underline-offset-8 transition-all">
                        Scriptderm</h3>
                    <p class="text-gray-500 mb-8 leading-relaxed text-lg">
                        Scriptderm is a dermatology-led skincare brand and direct-to-consumer platform.
                    </p>
                    <div class="flex gap-3">
                        <span
                            class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Branding</span>
                        <span
                            class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Web
                            Design</span>
                    </div>
                </div>
                <div class="lg:col-span-8">
                    <div
                        class="bg-[#F0F4FF] p-10 md:p-16 rounded-[2.5rem] hover-target transition-transform duration-700 hover:scale-[1.02]">
                        <img src="https://cdn.dribbble.com/userupload/45112486/file/fb44589cc3a03e665417884b37ddb2ea.png?resize=752x&vertical=center"
                            alt="Project"
                            class="w-full h-64 md:h-80 lg:h-[360px] object-cover object-center shadow-2xl rounded-xl">
                    </div>
                </div>
            </div>

            <div class="reveal reveal-zoom grid grid-cols-1 lg:grid-cols-12 gap-12 items-center group">
                <div class="lg:col-span-8 order-2 lg:order-1">
                    <div
                        class="bg-[#111] p-10 md:p-16 rounded-[2.5rem] hover-target transition-transform duration-700 hover:scale-[1.02]">
                        <img src="https://cdn.dribbble.com/userupload/42644800/file/original-92d9c35c71fc71fd537632162e325d6f.png?resize=752x&vertical=center"
                            alt="Project"
                            class="w-full h-64 md:h-80 lg:h-[360px] object-cover object-center shadow-2xl rounded-xl opacity-90">
                    </div>
                </div>
                <div class="lg:col-span-4 order-1 lg:order-2">
                    <div class="mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 bg-black rounded-full"></span>
                        <span class="text-sm font-bold">Web3, FR</span>
                    </div>
                    <h3
                        class="text-5xl font-bold mb-6 group-hover:underline decoration-2 underline-offset-8 transition-all">
                        Sophisticated Traders</h3>
                    <p class="text-gray-500 mb-8 leading-relaxed text-lg">
                        A proprietary trading and fintech collective that merges data science.
                    </p>
                    <div class="flex gap-3">
                        <span
                            class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Branding</span>
                        <span
                            class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Web
                            Design</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="pt-24 pb-0 px-6 bg-[#F3F4F6]">
        <div class="reveal reveal-left max-w-7xl mx-auto">
            <div class="mb-12 flex items-center gap-3">
                <span class="w-2 h-2 bg-black rounded-full"></span>
                <h3 class="text-sm font-bold uppercase tracking-widest">Our services</h3>
            </div>

            <div class="bg-white rounded-[3rem] p-8 md:p-16 shadow-sm">
                <div class="space-y-4">
                    @php
                        $services = [
                            [
                                'name' => 'Brand Design',
                                'description' =>
                                    'Membangun fondasi identitas visual bisnis Anda secara menyeluruh. Mencakup pembuatan Logo, Brand Guidelines, pemilihan Tipografi, Palet Warna, hingga penerapan pada Mockup Stationary.',
                            ],
                            [
                                'name' => 'UI/UX Mobile App',
                                'description' =>
                                    'Layanan ini mencakup desain Aplikasi Mobile atau Dashboard Sistem. Fokus utama adalah kemudahan penggunaan, estetika visual, dan alur navigasi yang efisien untuk meningkatkan konversi pengguna.',
                            ],
                            [
                                'name' => 'Motion Graphic (20s)',
                                'description' =>
                                    'Layanan ini meliputi pembuatan Logo Reveal, Animasi UI, Explainer Video pendek, atau aset animasi untuk kebutuhan konten media sosial (Reels/TikTok) dengan durasi maksimal 20 detik.',
                            ],
                            [
                                'name' => 'Illustration',
                                'description' =>
                                    'Pembuatan aset ilustrasi custom yang unik dan artistik. Cocok untuk kebutuhan aset website (hero image), ikonografi khusus, maskot brand, atau merchandise.',
                            ],
                            [
                                'name' => 'Graphic Design',
                                'description' =>
                                    'Solusi desain grafis harian untuk kebutuhan pemasaran cepat. Mencakup desain Feed/Story Instagram, Banner Iklan, Poster Acara, Flyer, atau Header Email.',
                            ],
                            [
                                'name' => 'Logo Design',
                                'description' =>
                                    'Layanan ini fokus pada pembuatan Logo (Logomark & Logotype) yang unik, filosofis, dan timeless. Khusus desain logo saja, tanpa full brand guidelines.',
                            ],
                            [
                                'name' => 'UI/UX Landing Page',
                                'description' =>
                                    'Desain halaman web satu muka (single-page) yang dirancang strategis untuk pemasaran. Fokus pada struktur konten persuasif dan visual memukau untuk meningkatkan konversi.',
                            ],
                        ];
                    @endphp

                    @foreach ($services as $index => $service)
                        <div
                            class="group relative flex items-center justify-between py-10 border-b border-gray-100 hover:border-black transition-colors duration-500 cursor-none hover-target overflow-hidden">

                            <div
                                class="flex items-center gap-6 md:gap-12 relative z-10 
                transform transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] 
                group-hover:translate-x-4 will-change-transform">

                                <span
                                    class="text-sm font-mono text-gray-300 w-8 transition-colors duration-500 group-hover:text-black">0{{ $index + 1 }}</span>
                                <span
                                    class="text-3xl md:text-5xl font-medium text-gray-900 transition-all duration-500 group-hover:font-bold">
                                    {{ $service['name'] }}
                                </span>
                            </div>

                            <div
                                class="absolute right-0 top-1/2 -translate-y-1/2 w-full md:w-1/2 lg:w-5/12 text-right pr-4 md:pr-8
                opacity-0 translate-x-12 
                group-hover:opacity-100 group-hover:translate-x-0 
                transform transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] 
                pointer-events-none will-change-transform">

                                <p
                                    class="text-sm md:text-base text-gray-600 leading-relaxed font-medium bg-white/95 p-4 rounded-xl shadow-sm inline-block">
                                    {{ $service['description'] }}
                                </p>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-24 px-6 bg-[#F3F4F6]">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 bg-black rounded-full"></span>
                    <h3 class="text-sm font-bold uppercase tracking-widest">Testimonials</h3>
                </div>

                <div class="flex gap-4">
                    <button id="prevTesti"
                        class="w-14 h-14 border border-gray-300 rounded-full flex items-center justify-center hover:bg-black hover:text-white hover:border-black transition-all hover-target bg-white">
                        <i data-feather="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <button id="nextTesti"
                        class="w-14 h-14 border border-gray-300 rounded-full flex items-center justify-center hover:bg-black hover:text-white hover:border-black transition-all hover-target bg-white">
                        <i data-feather="arrow-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="relative">
                <div id="testimonialTrack"
                    class="flex gap-8 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-10">

                    @php
                        $testimonials = [
                            [
                                'name' => 'Andi Pratama',
                                'role' => 'CEO, Kopi Senja',
                                'service' => 'Branding & Logo',
                                'img' => 'https://randomuser.me/api/portraits/men/32.jpg',
                                'text' =>
                                    "Gila sih, Vektora bener-bener nangkep visi gue. Branding baru Kopi Senja jadi lebih 'mahal' dan pelanggan banyak yang notice perubahannya. Sangat worth it!",
                            ],
                            [
                                'name' => 'Jessica Tan',
                                'role' => 'Marketing Lead, TechOne',
                                'service' => 'UI/UX Landing Page',
                                'img' => 'https://randomuser.me/api/portraits/women/44.jpg',
                                'text' =>
                                    'Konversi landing page kami naik 200% setelah di-redesign oleh tim Vektora. Susunan layout dan copywriting-nya bener-bener persuasif. Thanks team!',
                            ],
                            [
                                'name' => 'Budi Santoso',
                                'role' => 'Founder, E-Bike Indo',
                                'service' => 'UI/UX Mobile App',
                                'img' => 'https://randomuser.me/api/portraits/men/85.jpg',
                                'text' =>
                                    'Flow aplikasi yang dibuat sangat user-friendly. User kami yang gaptek pun bisa pakai aplikasinya dengan mudah. Desainnya clean dan modern banget.',
                            ],
                            [
                                'name' => 'Sarah Wijaya',
                                'role' => 'Owner, Glow Skin',
                                'service' => 'Social Media Design',
                                'img' => 'https://randomuser.me/api/portraits/women/65.jpg',
                                'text' =>
                                    'Feed Instagram jadi super rapi dan estetik. Engagement rate naik drastis karena visualnya eye-catching. Bakal langganan terus buat konten bulanan.',
                            ],
                            [
                                'name' => 'Reza Rahardian',
                                'role' => 'Director, ArtSpace',
                                'service' => 'Motion Graphic',
                                'img' => 'https://randomuser.me/api/portraits/men/22.jpg',
                                'text' =>
                                    'Video explainer yang dibuat Vektora sangat membantu menjelaskan produk kami yang rumit jadi sederhana. Animasinya halus dan transisinya keren abis.',
                            ],
                        ];
                    @endphp

                    @foreach ($testimonials as $testi)
                        <div
                            class="min-w-[100%] md:min-w-[calc(50%-16px)] snap-start bg-white p-10 md:p-12 rounded-[2.5rem] flex flex-col justify-between hover:shadow-xl transition-all duration-500 reveal group">

                            <div class="flex justify-between items-start mb-8">
                                <div class="flex text-yellow-400 gap-1">
                                    @for ($i = 0; $i < 5; $i++)
                                        <i data-feather="star" class="w-5 h-5 fill-current"></i>
                                    @endfor
                                </div>
                                <span
                                    class="px-4 py-1.5 bg-gray-100 rounded-full text-xs font-bold uppercase tracking-wider text-gray-600 group-hover:bg-black group-hover:text-white transition-colors">
                                    {{ $testi['service'] }}
                                </span>
                            </div>

                            <p class="text-xl md:text-2xl text-gray-800 leading-relaxed font-medium mb-10">
                                "{{ $testi['text'] }}"
                            </p>

                            <div class="flex items-center gap-4 mt-auto pt-8 border-t border-gray-100">
                                <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-gray-100">
                                    <img src="{{ $testi['img'] }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h5 class="font-bold text-lg text-black">{{ $testi['name'] }}</h5>
                                    <span class="text-sm text-gray-500 font-medium">{{ $testi['role'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('testimonialTrack');
            const prevBtn = document.getElementById('prevTesti');
            const nextBtn = document.getElementById('nextTesti');
            let autoScroll;

            // Fungsi Hitung Jarak Scroll per Card
            const getScrollAmount = () => {
                const card = track.querySelector('div');
                const gap = 32; // sesuai gap-8 tailwind
                return card.offsetWidth + gap;
            };

            // Fungsi Next (dengan Logika Looping)
            const handleNext = () => {
                const maxScrollLeft = track.scrollWidth - track.clientWidth;

                // Jika sisa scroll kurang dari 10px (sudah mentok kanan), balik ke awal
                if (track.scrollLeft >= maxScrollLeft - 10) {
                    track.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    track.scrollBy({
                        left: getScrollAmount(),
                        behavior: 'smooth'
                    });
                }
            };

            // Fungsi Prev (dengan Logika Looping)
            const handlePrev = () => {
                // Jika di posisi awal (0), lompat ke paling ujung kanan
                if (track.scrollLeft <= 0) {
                    track.scrollTo({
                        left: track.scrollWidth,
                        behavior: 'smooth'
                    });
                } else {
                    track.scrollBy({
                        left: -getScrollAmount(),
                        behavior: 'smooth'
                    });
                }
            };

            // Event Listeners Tombol
            nextBtn.addEventListener('click', handleNext);
            prevBtn.addEventListener('click', handlePrev);

            // AUTO SCROLL SETUP
            const startAutoScroll = () => {
                autoScroll = setInterval(handleNext, 3000); // Geser tiap 3 detik
            };

            const stopAutoScroll = () => {
                clearInterval(autoScroll);
            };

            // Jalankan Auto Scroll saat halaman dimuat
            startAutoScroll();

            // Fitur UX: Stop auto scroll saat mouse berada di atas area testimoni/tombol
            // Agar user bisa baca dengan tenang
            const interactables = [track, nextBtn, prevBtn];
            interactables.forEach(el => {
                el.addEventListener('mouseenter', stopAutoScroll);
                el.addEventListener('mouseleave', startAutoScroll);
            });
        });
    </script>

    <section class="py-32 px-6 bg-white rounded-t-[4rem]">
        <div class="max-w-7xl mx-auto">
            <div class="mb-12 flex items-center gap-3">
                <span class="w-2 h-2 bg-black rounded-full"></span>
                <h3 class="text-sm font-bold uppercase tracking-widest">We're explorers</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
                <div>
                    <h2 class="reveal text-6xl md:text-8xl font-bold mb-10 leading-none tracking-tighter">Ready to take <br> next
                        step <br> with us?</h2>
                    <div
                        class="inline-flex px-8 py-4 border rounded-full text-gray-500 hover:text-black hover:border-black transition-colors hover-target cursor-pointer items-center gap-2">
                        Contact us <i data-feather="arrow-down-right" class="w-4 h-4"></i>
                    </div>
                </div>

                <div class="bg-white">
                    <h3 class="text-4xl font-bold mb-10">Let's make an impact</h3>
                    <form class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="group relative">
                                <input type="text" placeholder=" "
                                    class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                                <label
                                    class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Name</label>
                            </div>
                            <div class="group relative">
                                <input type="text" placeholder=" "
                                    class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                                <label
                                    class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Company</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="group relative">
                                <input type="email" placeholder=" "
                                    class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                                <label
                                    class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Email</label>
                            </div>
                            <div class="group relative">
                                <input type="text" placeholder=" "
                                    class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                                <label
                                    class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Phone</label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase mb-6 text-gray-400">I'm interested
                                in...</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach (['Branding', 'Website Design', 'UX/UI', 'Motion Design', 'Landing page', 'Content Creation'] as $tag)
                                    <label class="cursor-pointer hover-target">
                                        <input type="checkbox" class="peer sr-only">
                                        <span
                                            class="px-6 py-3 border rounded-full text-sm block transition-all peer-checked:bg-black peer-checked:text-white peer-checked:border-black hover:border-black">
                                            {{ $tag }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button
                            class="w-full py-5 bg-black text-white rounded-full font-bold text-lg btn-invert btn-black hover-target mt-8">
                            Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
