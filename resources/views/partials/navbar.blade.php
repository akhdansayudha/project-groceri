<nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-md border-b border-gray-200 transition-all"
    id="main-navbar">
    <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center relative z-50 bg-white/0">

        {{-- LOGO --}}
        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter hover-target">
            vektora<span class="text-blue-600">.</span>
        </a>

        {{-- DESKTOP MENU --}}
        <div class="hidden md:flex space-x-16 items-center">
            <a href="{{ route('home') }}"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Home</a>
            <a href="#who-we-are"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">About</a>

            {{-- Dropdown Services --}}
            <div class="group static">
                <button
                    class="text-sm font-medium hover:text-gray-500 transition-colors flex items-center gap-1 hover-target py-2">
                    Services <i data-feather="chevron-down" class="w-4 h-4"></i>
                </button>
                <div
                    class="mega-menu invisible opacity-0 absolute top-[80px] left-1/2 -translate-x-1/2 w-[90vw] max-w-4xl bg-white rounded-3xl p-10 shadow-2xl border border-gray-100 z-40 transition-all duration-300 group-hover:visible group-hover:opacity-100 group-hover:top-[70px]">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <h4 class="text-gray-400 text-xs uppercase tracking-widest mb-6 font-bold">Design</h4>
                            <ul class="space-y-4">
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">Branding</a>
                                </li>
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">Website
                                        Design</a></li>
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">UI/UX</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-xs uppercase tracking-widest mb-6 font-bold">Development</h4>
                            <ul class="space-y-4">
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">Webflow
                                        Dev</a></li>
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">Shopify</a>
                                </li>
                                <li><a href="#"
                                        class="text-xl font-medium hover:text-blue-600 hover-target block">React /
                                        Vue</a></li>
                            </ul>
                        </div>
                        <div class="hidden md:block bg-gray-100 rounded-xl h-full min-h-[150px]"></div>
                    </div>
                </div>
            </div>

            <a href="#featured"
                class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Projects</a>
        </div>

        {{-- BUTTON DESKTOP --}}
        <a href="{{ route('login') }}"
            class="hidden md:inline-block px-6 py-2.5 rounded-full text-sm font-bold btn-invert btn-black hover-target">
            Client Login
        </a>

        {{-- BURGER MENU BUTTON (Dinamis Animasi) --}}
        <button id="burger-btn"
            class="md:hidden p-2 hover-target relative w-10 h-10 flex justify-center items-center group focus:outline-none">
            {{-- Icon Burger (Menu) --}}
            <span id="icon-burger"
                class="absolute transition-all duration-300 ease-in-out transform scale-100 rotate-0 opacity-100">
                <i data-feather="menu" class="w-6 h-6"></i>
            </span>
            {{-- Icon Close (X) --}}
            <span id="icon-close"
                class="absolute transition-all duration-300 ease-in-out transform scale-0 -rotate-90 opacity-0 text-black">
                <i data-feather="x" class="w-6 h-6"></i>
            </span>
        </button>
    </div>
</nav>

{{-- MOBILE MENU OVERLAY (Slide dari Bawah Navbar) --}}
{{-- Z-Index 40: Agar berada di BAWAH Navbar (Z-50) --}}
<div id="mobile-menu"
    class="fixed inset-0 z-40 bg-white transition-transform duration-500 ease-[cubic-bezier(0.77,0,0.175,1)] -translate-y-full pt-[90px] flex flex-col md:hidden">

    <div class="flex-1 flex flex-col px-6 pb-10 overflow-y-auto">
        {{-- Menu Links --}}
        <nav class="flex flex-col gap-6 mt-10">
            <div class="group border-b border-gray-100 pb-4">
                <a href="{{ route('home') }}"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Home
                </a>
            </div>
            <div class="group border-b border-gray-100 pb-4">
                <a href="#who-we-are"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    About
                </a>
            </div>
            <div class="group border-b border-gray-100 pb-4">
                <a href="#services"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Services
                </a>
            </div>
            <div class="group border-b border-gray-100 pb-4">
                <a href="#featured"
                    class="mobile-link block text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors tracking-tight">
                    Projects
                </a>
            </div>
        </nav>

        {{-- Call to Action Mobile --}}
        <div class="mt-10">
            <a href="{{ route('login') }}"
                class="flex w-full items-center justify-between px-8 py-5 rounded-full text-xl font-bold bg-black text-white hover:scale-[1.02] transition-transform shadow-xl">
                Client Login <i data-feather="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>

        {{-- Footer Info --}}
        <div class="mt-auto pt-10">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-4">Contact</p>
            <a href="mailto:hello@vektora.agency"
                class="text-lg font-medium text-gray-600 block mb-2">hello@vektora.agency</a>
            <p class="text-lg font-medium text-gray-600">+62 812 3456 7890</p>

            <div class="flex gap-6 mt-8">
                <a href="#" class="text-gray-400 hover:text-black transition-colors"><i data-feather="instagram"
                        class="w-6 h-6"></i></a>
                <a href="#" class="text-gray-400 hover:text-black transition-colors"><i data-feather="linkedin"
                        class="w-6 h-6"></i></a>
                <a href="#" class="text-gray-400 hover:text-black transition-colors"><i data-feather="twitter"
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
                // Buka Menu (Slide Down)
                mobileMenu.classList.remove('-translate-y-full');
                mobileMenu.classList.add('translate-y-0');

                // Animasi Icon: Burger hilang, Close muncul
                iconBurger.classList.remove('scale-100', 'rotate-0', 'opacity-100');
                iconBurger.classList.add('scale-0', 'rotate-90', 'opacity-0');

                iconClose.classList.remove('scale-0', '-rotate-90', 'opacity-0');
                iconClose.classList.add('scale-100', 'rotate-0', 'opacity-100');

                document.body.style.overflow = 'hidden'; // Kunci scroll body
            } else {
                // Tutup Menu (Slide Up)
                mobileMenu.classList.remove('translate-y-0');
                mobileMenu.classList.add('-translate-y-full');

                // Animasi Icon: Close hilang, Burger muncul
                iconBurger.classList.remove('scale-0', 'rotate-90', 'opacity-0');
                iconBurger.classList.add('scale-100', 'rotate-0', 'opacity-100');

                iconClose.classList.remove('scale-100', 'rotate-0', 'opacity-100');
                iconClose.classList.add('scale-0', '-rotate-90', 'opacity-0');

                document.body.style.overflow = ''; // Buka scroll body
            }
        }

        burgerBtn.addEventListener('click', toggleMenu);

        // Auto close saat link diklik
        menuLinks.forEach(link => {
            link.addEventListener('click', toggleMenu);
        });

        feather.replace();
    });
</script>
