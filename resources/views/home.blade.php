@extends('layouts.app')

@section('content')
    <style>
        /* Sembunyikan Custom Cursor di Device Touchscreen */
        @media (pointer: coarse) {

            .cursor-dot,
            .cursor-outline {
                display: none !important;
            }

            * {
                cursor: auto !important;
            }
        }

        /* Animasi Accordion Mobile */
        .service-desc-mobile {
            transition: max-height 0.5s ease-out, opacity 0.5s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        .service-desc-mobile.active {
            opacity: 1;
        }

        /* Animasi Background Gradient Bergerak */
        @keyframes gradient-xy {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .animate-gradient-xy {
            background-size: 200% 200%;
            animation: gradient-xy 6s ease infinite;
        }

        /* Animasi Teks Gradient Mengkilap */
        @keyframes gradient-x {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }

        .animate-gradient-x {
            background-size: 200% auto;
            animation: gradient-x 4s linear infinite;
        }
    </style>

    <section class="h-screen w-full flex flex-col justify-center items-center relative overflow-hidden bg-white">

        <div class="text-center max-w-6xl mx-auto z-10 -mt-10 px-6">
            <h1 class="reveal text-6xl md:text-8xl lg:text-9xl font-bold leading-[1] mb-8 tracking-tighter">
                Your trusted <br> creative partner.
            </h1>
            <p class="reveal text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
                We deliver creative branding, web design, and UI/UX solutions to make the most impact for your business.
            </p>

            <div class="reveal flex gap-4 justify-center">
                {{-- TOMBOL MOBILE: Client Login --}}
                <a href="{{ route('login') }}"
                    class="md:hidden px-8 py-4 rounded-full font-bold btn-invert btn-black hover-target flex items-center gap-2">
                    Client Login <i data-feather="log-in" class="w-4 h-4"></i>
                </a>

                {{-- TOMBOL DESKTOP: Request a Quote (Scroll ke bawah) --}}
                <a href="#collaborate"
                    class="hidden md:flex px-8 py-4 rounded-full font-bold btn-invert btn-black hover-target items-center gap-2">
                    Let's Collaborate <span>🤝</span>
                </a>
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
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
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
                    <a href="#our-team"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-black text-white rounded-full font-bold btn-invert btn-black hover-target">
                        Our Team <i data-feather="arrow-right" class="w-4 h-4"></i>
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

            {{-- Project 1 --}}
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

            {{-- Project 2 --}}
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
                <div class="space-y-4" id="services-container">
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
                        {{-- SERVICE ITEM --}}
                        <div class="service-item group relative flex flex-col md:flex-row md:items-center justify-between py-6 md:py-10 border-b border-gray-100 hover:border-black transition-colors duration-500 cursor-pointer hover-target overflow-hidden"
                            onclick="toggleService(this)">

                            {{-- TITLE SECTION --}}
                            <div
                                class="flex items-center gap-6 md:gap-12 relative z-10 w-full md:w-auto
                                transform transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] 
                                md:group-hover:translate-x-4 will-change-transform">

                                <span
                                    class="text-sm font-mono text-gray-300 w-8 transition-colors duration-500 group-hover:text-black">0{{ $index + 1 }}</span>
                                <span
                                    class="text-2xl md:text-3xl lg:text-5xl font-medium text-gray-900 transition-all duration-500 group-hover:font-bold">
                                    {{ $service['name'] }}
                                </span>
                                {{-- Chevron for Mobile Indication --}}
                                <span
                                    class="ml-auto md:hidden text-gray-300 transition-transform duration-300 chevron-icon">
                                    <i data-feather="chevron-down"></i>
                                </span>
                            </div>

                            {{-- DESKRIPSI (DESKTOP) --}}
                            <div
                                class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 w-1/2 lg:w-5/12 text-right pr-4 md:pr-8
                                opacity-0 translate-x-12 
                                group-hover:opacity-100 group-hover:translate-x-0 
                                transform transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] 
                                pointer-events-none will-change-transform">
                                <p
                                    class="text-base text-gray-600 leading-relaxed font-medium bg-white/95 p-4 rounded-xl shadow-sm inline-block">
                                    {{ $service['description'] }}
                                </p>
                            </div>

                            {{-- DESKRIPSI (MOBILE ACCORDION) --}}
                            <div class="service-desc-mobile block md:hidden w-full mt-2">
                                <p class="text-sm text-gray-600 leading-relaxed font-medium bg-gray-50 p-4 rounded-xl">
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
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
                    {{-- Dummy Data Testimoni --}}
                    @php
                        $testimonials = [
                            [
                                'name' => 'Andi Pratama',
                                'role' => 'CEO, Kopi Senja',
                                'service' => 'Branding',
                                'text' =>
                                    "Gila sih, Vektora bener-bener nangkep visi gue. Branding baru Kopi Senja jadi lebih 'mahal'.",
                            ],
                            [
                                'name' => 'Jessica Tan',
                                'role' => 'Marketing Lead',
                                'service' => 'UI/UX',
                                'text' => 'Konversi landing page kami naik 200% setelah di-redesign oleh tim Vektora.',
                            ],
                            [
                                'name' => 'Budi Santoso',
                                'role' => 'Founder',
                                'service' => 'Mobile App',
                                'text' =>
                                    'Flow aplikasi yang dibuat sangat user-friendly. User kami yang gaptek pun bisa pakai.',
                            ],
                            [
                                'name' => 'Sarah Wijaya',
                                'role' => 'Founder, GlowUp Skin',
                                'service' => 'Graphic Design',
                                'text' =>
                                    "Desain feed Instagram jadi super aesthetic dan rapi. Engagement followers kami langsung naik drastis sejak pakai jasa Vektora.",
                            ],
                            [
                                'name' => 'Rian Hidayat',
                                'role' => 'CTO, TechNesia',
                                'service' => 'Web Design',
                                'text' =>
                                    "Website baru performanya kenceng dan desainnya futuristik abis. Bikin kredibilitas startup kami makin oke di mata investor.",
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
                                    class="px-4 py-1.5 bg-gray-100 rounded-full text-xs font-bold uppercase tracking-wider text-gray-600">
                                    {{ $testi['service'] }}
                                </span>
                            </div>
                            <p class="text-xl md:text-2xl text-gray-800 leading-relaxed font-medium mb-10">
                                "{{ $testi['text'] }}"</p>
                            <div class="flex items-center gap-4 mt-auto pt-8 border-t border-gray-100">
                                <div class="w-12 h-12 rounded-full bg-gray-200"></div>
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

    {{-- SECTION ID TEAM & CTA --}}
    <section id="our-team" class="py-32 px-6 bg-white rounded-t-[4rem]">
        <div class="max-w-7xl mx-auto">

            {{-- HEADER TEAM --}}
            <div class="mb-20 flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-2 h-2 bg-black rounded-full"></span>
                        <h3 class="text-sm font-bold uppercase tracking-widest">Our Team</h3>
                    </div>
                    <h2 class="reveal text-5xl md:text-7xl font-bold tracking-tight leading-[1]">
                        The minds behind <br> the magic.
                    </h2>
                </div>
                <p class="reveal text-gray-500 max-w-md text-lg leading-relaxed mb-2">
                    A collective of thinkers, dreamers, and doers committed to crafting digital excellence.
                </p>
            </div>

            {{-- GRID TEAM MEMBERS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-16 mb-32">
                @php
                    $teamMembers = [
                        [
                            'name' => 'Muhammad Naufal Fahrezy',
                            'role' => 'Founder & Creative Director',
                            'img' =>
                                'https://azgwfpkdujdvpfxnnieb.supabase.co/storage/v1/object/public/chat-attachments/foto_fahrezy2.png',
                            'linkedin' => 'https://www.linkedin.com/in/muhammad-naufal-fahrezy',
                        ],
                        [
                            'name' => 'Akhdan Sayudha Laksmana',
                            'role' => 'Head of Technology',
                            'img' =>
                                'https://azgwfpkdujdvpfxnnieb.supabase.co/storage/v1/object/public/chat-attachments/foto_akhdan2.jpeg',
                            'linkedin' => 'https://www.linkedin.com/in/akhdan-sayudha-laksmana',
                        ],
                        [
                            'name' => 'Wahyudi Tri Susanto',
                            'role' => 'Lead Product Designer',
                            'img' =>
                                'https://azgwfpkdujdvpfxnnieb.supabase.co/storage/v1/object/public/chat-attachments/foto_wahyudi.jpeg',
                            'linkedin' => 'https://www.linkedin.com/in/wahyudi-tri-susanto',
                        ],
                    ];
                @endphp

                @foreach ($teamMembers as $member)
                    <div class="reveal group cursor-default"> {{-- cursor-pointer diganti default agar tidak bingung --}}

                        {{-- Image Container --}}
                        <div class="relative overflow-hidden rounded-[2.5rem] mb-8 aspect-[4/5] bg-gray-100">
                            <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}"
                                class="w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110 grayscale group-hover:grayscale-0">

                            {{-- Social Overlay --}}
                            <div
                                class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">

                                {{-- BUTTON LINKEDIN (Ubah DIV jadi A) --}}
                                <a href="{{ $member['linkedin'] }}" target="_blank" rel="noopener noreferrer"
                                    class="bg-white/90 backdrop-blur rounded-full px-6 py-3 transform translate-y-10 group-hover:translate-y-0 transition-all duration-500 hover:bg-black hover:text-white flex items-center gap-2 shadow-lg">
                                    <span class="text-sm font-bold">Connect LinkedIn</span>
                                    <i data-feather="linkedin" class="w-4 h-4"></i>
                                </a>

                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col">
                            <h4 class="text-2xl font-bold text-black group-hover:text-blue-600 transition-colors">
                                {{ $member['name'] }}
                            </h4>
                            <span class="text-gray-500 font-medium mt-1">{{ $member['role'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- CTA SECTION (Redesigned) --}}
            <div id="collaborate" class="reveal relative mt-32 group scroll-mt-10">

                {{-- 1. Outer Glow Container --}}
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-[3.5rem] blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200 animate-gradient-xy">
                </div>

                {{-- 2. Main Card Content --}}
                <div
                    class="relative bg-[#050505] rounded-[3rem] px-8 py-24 md:p-32 text-center overflow-hidden border border-white/10">

                    {{-- Background Effects --}}
                    <div class="absolute inset-0 w-full h-full">
                        {{-- Grid Pattern --}}
                        <div
                            class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]">
                        </div>

                        {{-- Animated Blobs (Aurora Effect) --}}
                        <div
                            class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-[120px] animate-pulse">
                        </div>
                        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-[120px] animate-pulse"
                            style="animation-delay: 1s"></div>
                    </div>

                    {{-- Content Wrapper --}}
                    <div class="relative z-10 max-w-5xl mx-auto flex flex-col items-center">

                        {{-- Badge --}}
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-8">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-white tracking-widest uppercase">Open for
                                Collaboration</span>
                        </div>

                        {{-- Title --}}
                        <h2 class="text-5xl md:text-8xl font-bold text-white mb-8 tracking-tighter leading-[0.9]">
                            Have an idea? <br>
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-gray-400 via-white to-gray-400 animate-gradient-x">Let's
                                build it.</span>
                        </h2>

                        <p class="text-gray-400 text-lg md:text-xl mb-12 max-w-2xl mx-auto leading-relaxed">
                            Don't let your vision stay a dream. Join hundreds of visionary brands that have transformed
                            their digital presence with Vektora.
                        </p>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <a href="{{ route('login') }}"
                                class="group/btn relative inline-flex items-center gap-3 px-12 py-6 bg-white text-black rounded-full font-bold text-lg overflow-hidden transition-all hover:scale-105 hover:shadow-[0_0_40px_rgba(255,255,255,0.4)]">
                                <span class="relative z-10">Start Your Project</span>
                                <i data-feather="arrow-right"
                                    class="relative z-10 w-5 h-5 group-hover/btn:translate-x-1 transition-transform"></i>
                                {{-- Button Hover Fill --}}
                                <div
                                    class="absolute inset-0 bg-gray-200 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform origin-left duration-300">
                                </div>
                            </a>

                            <a href="https://wa.me/6281234567890" target="_blank"
                                class="inline-flex items-center gap-3 px-10 py-6 text-white font-bold text-lg hover:text-gray-300 transition-colors group/link">
                                <span>Consultation first</span>
                                <i data-feather="message-circle"
                                    class="w-5 h-5 group-hover/link:rotate-12 transition-transform"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Inisialisasi Feather Icons
            feather.replace();

            // --- LOGIC TESTIMONIAL CAROUSEL (BARU) ---
            const track = document.getElementById('testimonialTrack');
            const btnPrev = document.getElementById('prevTesti');
            const btnNext = document.getElementById('nextTesti');

            // Cek apakah elemen ada untuk menghindari error
            if (track && btnPrev && btnNext) {

                // Fungsi Scroll
                const scrollCarousel = (direction) => {
                    // Ambil lebar kartu pertama + gap (gap-8 = 32px)
                    const cardWidth = track.firstElementChild.offsetWidth + 32;

                    if (direction === 'next') {
                        // Jika sudah di ujung kanan, kembali ke awal
                        if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                            track.scrollTo({
                                left: 0,
                                behavior: 'smooth'
                            });
                        } else {
                            track.scrollBy({
                                left: cardWidth,
                                behavior: 'smooth'
                            });
                        }
                    } else {
                        track.scrollBy({
                            left: -cardWidth,
                            behavior: 'smooth'
                        });
                    }
                };

                // Event Listener Tombol
                btnNext.addEventListener('click', () => scrollCarousel('next'));
                btnPrev.addEventListener('click', () => scrollCarousel('prev'));

                // --- AUTO SCROLL FEATURE ---
                let autoScrollInterval;

                const startAutoScroll = () => {
                    autoScrollInterval = setInterval(() => {
                        scrollCarousel('next');
                    }, 4000); // Geser setiap 4 detik
                };

                const stopAutoScroll = () => {
                    clearInterval(autoScrollInterval);
                };

                // Jalankan Auto Scroll saat halaman dimuat
                startAutoScroll();

                // Berhenti Auto Scroll saat mouse diarahkan ke testimonial (agar user bisa baca)
                track.addEventListener('mouseenter', stopAutoScroll);
                // Lanjut Auto Scroll saat mouse keluar
                track.addEventListener('mouseleave', startAutoScroll);
            }

            // --- SERVICES ACCORDION LOGIC (MOBILE) ---
            window.toggleService = function(element) {
                if (window.innerWidth >= 768) return; // Only mobile

                const desc = element.querySelector('.service-desc-mobile');
                const chevron = element.querySelector('.chevron-icon');
                const isActive = desc.classList.contains('active');

                // 1. Close ALL others
                document.querySelectorAll('.service-desc-mobile').forEach(d => {
                    d.style.maxHeight = null;
                    d.classList.remove('active');
                });
                document.querySelectorAll('.chevron-icon').forEach(c => {
                    c.style.transform = 'rotate(0deg)';
                });

                // 2. Open clicked if not already open
                if (!isActive) {
                    desc.classList.add('active');
                    desc.style.maxHeight = desc.scrollHeight + "px";
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            };
        });
    </script>
@endsection
