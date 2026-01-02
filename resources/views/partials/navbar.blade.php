<nav class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-lg border-b border-gray-200/50 transition-all"
    id="main-navbar">

    {{-- LOGIC PENENTUAN LINK --}}
    @php
        // Cek apakah user sedang berada di halaman Home
        $isHome = request()->routeIs('home');

        // Jika di Home, prefix kosong (misal: "#services").
        // Jika di halaman lain, prefix url home (misal: "http://vektora.test/#services")
        $hashPrefix = $isHome ? '' : route('home');
    @endphp

    <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center relative z-50">

        {{-- LOGO --}}
        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter hover-target">
            vektora<span class="text-blue-600">.</span>
        </a>

        {{-- DESKTOP MENU --}}
        <div class="hidden md:flex space-x-12 items-center">

            {{-- Link Home selalu mengarah ke route home --}}
            <a href="{{ route('home') }}"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Home</a>

            {{-- UPDATE LINK: About --}}
            <a href="{{ $hashPrefix }}#who-we-are"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">About</a>

            {{-- UPDATE LINK: Projects --}}
            <a href="{{ $hashPrefix }}#featured"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Projects</a>

            {{-- Dropdown Services --}}
            <div class="group static" id="services-group">
                <button
                    class="text-sm font-medium hover:text-gray-500 transition-colors flex items-center gap-1 hover-target py-4">
                    Services <i data-feather="chevron-down"
                        class="w-4 h-4 transition-transform group-hover:rotate-180"></i>
                </button>

                {{-- Mega Menu Container --}}
                <div
                    class="invisible opacity-0 absolute top-[85px] left-1/2 -translate-x-1/2 w-screen max-w-6xl 
                    bg-white/100 backdrop-blur-3xl border border-white/20 shadow-xl rounded-[2rem] p-8 
                    transition-all duration-300 transform origin-top scale-95 
                    group-hover:visible group-hover:opacity-100 group-hover:scale-100 group-hover:top-[85px]">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        @php
                            $navServices = [
                                [
                                    'name' => 'Brand Design',
                                    'desc' => 'Visual identity & guidelines.',
                                    'icon' => 'pen-tool',
                                ],
                                [
                                    'name' => 'UI/UX Mobile App',
                                    'desc' => 'User-centric mobile interfaces.',
                                    'icon' => 'smartphone',
                                ],
                                ['name' => 'Motion Graphic', 'desc' => 'Engaging 2D/3D animations.', 'icon' => 'video'],
                                ['name' => 'Illustration', 'desc' => 'Custom artistic assets.', 'icon' => 'edit-2'],
                                [
                                    'name' => 'Graphic Design',
                                    'desc' => 'Marketing & social media kits.',
                                    'icon' => 'layers',
                                ],
                                [
                                    'name' => 'Logo Design',
                                    'desc' => 'Memorable logomark creation.',
                                    'icon' => 'hexagon',
                                ],
                                [
                                    'name' => 'Web Design',
                                    'desc' => 'High-converting landing pages.',
                                    'icon' => 'monitor',
                                ],
                            ];
                        @endphp

                        @foreach ($navServices as $service)
                            {{-- UPDATE LINK: Services Item --}}
                            <a href="{{ $hashPrefix }}#services"
                                class="group/item flex items-start gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all duration-300 hover-target">

                                <div
                                    class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-600 group-hover/item:bg-black group-hover/item:text-white transition-colors duration-300 flex-shrink-0">
                                    <i data-feather="{{ $service['icon'] }}" class="w-5 h-5"></i>
                                </div>

                                <div>
                                    <h4
                                        class="font-bold text-gray-900 group-hover/item:text-blue-600 transition-colors mb-1">
                                        {{ $service['name'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                        {{ $service['desc'] }}
                                    </p>
                                </div>
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- UPDATE LINK: Our Team --}}
            {{-- Pastikan section ID #our-team ada di home.blade.php, jika belum ada ganti ke section yang ada --}}
            <a href="{{ $hashPrefix }}#our-team"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Our Team</a>

            <a href="{{ route('pricing.index') }}"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Pricing</a>
        </div>

        {{-- BUTTON DESKTOP --}}
        <a href="{{ route('login') }}"
            class="hidden md:inline-block px-6 py-2.5 rounded-full text-sm font-bold btn-invert btn-black hover-target">
            Client Login
        </a>

        {{-- BURGER MENU BUTTON (Mobile) --}}
        <button id="burger-btn"
            class="md:hidden p-2 hover-target relative w-10 h-10 flex justify-center items-center group focus:outline-none">
            <span id="icon-burger"
                class="absolute transition-all duration-300 ease-in-out transform scale-100 rotate-0 opacity-100">
                <i data-feather="menu" class="w-6 h-6"></i>
            </span>
            <span id="icon-close"
                class="absolute transition-all duration-300 ease-in-out transform scale-0 -rotate-90 opacity-0 text-black">
                <i data-feather="x" class="w-6 h-6"></i>
            </span>
        </button>
    </div>
</nav>

{{-- MOBILE MENU OVERLAY --}}
<div id="mobile-menu"
    class="fixed inset-0 z-40 bg-white transition-transform duration-500 ease-[cubic-bezier(0.77,0,0.175,1)] -translate-y-full pt-[90px] flex flex-col md:hidden">

    <div class="flex-1 flex flex-col px-6 pb-10 overflow-y-auto">
        <nav class="flex flex-col gap-6 mt-10">
            <div class="group border-b border-gray-100 pb-4">
                <a href="{{ route('home') }}"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Home
                </a>
            </div>

            {{-- UPDATE LINK MOBILE: About --}}
            <div class="group border-b border-gray-100 pb-4">
                <a href="{{ $hashPrefix }}#who-we-are"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    About
                </a>
            </div>

            {{-- UPDATE LINK MOBILE: Services --}}
            <div class="group border-b border-gray-100 pb-4">
                <a href="{{ $hashPrefix }}#services"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Services
                </a>
            </div>

            {{-- UPDATE LINK MOBILE: Projects --}}
            <div class="group border-b border-gray-100 pb-4">
                <a href="{{ $hashPrefix }}#featured"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Projects
                </a>
            </div>
        </nav>

        <div class="mt-10">
            <a href="{{ route('login') }}"
                class="flex w-full items-center justify-between px-8 py-5 rounded-full text-xl font-bold bg-black text-white hover:scale-[1.02] transition-transform shadow-xl">
                Client Login <i data-feather="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>

        <div class="mt-auto pt-10">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-4">Contact</p>
            <a href="mailto:hello@vektora.agency"
                class="text-lg font-medium text-gray-600 block mb-2">hello@vektora.agency</a>
            <div class="flex gap-6 mt-8">
                <a href="#" class="text-gray-400 hover:text-black transition-colors"><i data-feather="instagram"
                        class="w-6 h-6"></i></a>
                <a href="#" class="text-gray-400 hover:text-black transition-colors"><i data-feather="linkedin"
                        class="w-6 h-6"></i></a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const burgerBtn = document.getElementById('burger-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconBurger = document.getElementById('icon-burger');
        const iconClose = document.getElementById('icon-close');
        const menuLinks = document.querySelectorAll('.mobile-link');
        let isMenuOpen = false;

        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('-translate-y-full');
                mobileMenu.classList.add('translate-y-0');
                iconBurger.classList.replace('scale-100', 'scale-0');
                iconBurger.classList.replace('opacity-100', 'opacity-0');
                iconBurger.classList.add('rotate-90');
                iconClose.classList.replace('scale-0', 'scale-100');
                iconClose.classList.replace('opacity-0', 'opacity-100');
                iconClose.classList.remove('-rotate-90');
                document.body.style.overflow = 'hidden';
            } else {
                mobileMenu.classList.remove('translate-y-0');
                mobileMenu.classList.add('-translate-y-full');
                iconBurger.classList.replace('scale-0', 'scale-100');
                iconBurger.classList.replace('opacity-0', 'opacity-100');
                iconBurger.classList.remove('rotate-90');
                iconClose.classList.replace('scale-100', 'scale-0');
                iconClose.classList.replace('opacity-100', 'opacity-0');
                iconClose.classList.add('-rotate-90');
                document.body.style.overflow = '';
            }
        }

        burgerBtn.addEventListener('click', toggleMenu);
        menuLinks.forEach(link => link.addEventListener('click', toggleMenu));

        // Logic Auto Close Hover (Tetap Sama)
        const servicesGroup = document.getElementById('services-group');
        const serviceLinks = servicesGroup.querySelectorAll('a');

        serviceLinks.forEach(link => {
            link.addEventListener('click', () => {
                servicesGroup.classList.remove('group');
            });
        });

        servicesGroup.addEventListener('mouseleave', () => {
            if (!servicesGroup.classList.contains('group')) {
                servicesGroup.classList.add('group');
            }
        });

        feather.replace();
    });
</script>
