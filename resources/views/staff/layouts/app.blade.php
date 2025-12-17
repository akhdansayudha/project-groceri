<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Portal - Vektora</title>

    {{-- 1. TAILWIND CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    }
                }
            }
        }
    </script>

    {{-- 2. ALPINE JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- 3. FEATHER ICONS --}}
    <script src="https://unpkg.com/feather-icons"></script>

    {{-- 4. SCRIPT ANTI-KILAT SIDEBAR (WAJIB DI HEAD) --}}
    <script>
        // Cek LocalStorage SEBELUM halaman dirender
        if (localStorage.getItem('staffSidebarState') === 'closed') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #374151;
            border-radius: 20px;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- LOGIKA CSS SIDEBAR (Mencegah FOUC) --- */
        /* Jika class 'sidebar-closed' ada di HTML, paksa lebar jadi 0 */
        html.sidebar-closed #sidebar-wrapper {
            width: 0 !important;
            border-right: none !important;
        }

        /* Mencegah animasi transisi saat halaman baru dimuat */
        .preload * {
            transition: none !important;
        }
    </style>
</head>

{{-- Tambahkan class 'preload' agar tidak ada animasi geser saat refresh --}}

<body class="bg-gray-100 font-sans antialiased text-gray-900 preload">
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR WRAPPER --}}
        {{-- Default w-72, akan di-override oleh CSS di head jika closed --}}
        <div id="sidebar-wrapper"
            class="w-72 transition-all duration-300 ease-in-out overflow-hidden flex-shrink-0 border-r border-gray-200 bg-white">
            <div class="w-72">
                @include('staff.partials.sidebar')
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        {{-- Gunakan flex-1 agar otomatis melebar saat sidebar menutup --}}
        <main class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden h-full transition-all duration-300">

            {{-- HEADER --}}
            @include('staff.partials.header')

            {{-- CONTENT --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 fade-in">
                @yield('content')

                <footer class="mt-10 text-center text-xs text-gray-400 pb-4">
                    &copy; {{ date('Y') }} Vektora Creative Agency.
                </footer>
            </div>
        </main>
    </div>

    <script>
        feather.replace();

        // --- HAPUS CLASS PRELOAD SETELAH LOAD ---
        window.addEventListener('load', () => {
            document.body.classList.remove('preload');
        });

        // --- LOGIC TOGGLE SIDEBAR ---
        const toggleBtn = document.getElementById('sidebar-toggle');

        function toggleSidebar() {
            // Toggle class di tag HTML agar CSS di head bereaksi
            document.documentElement.classList.toggle('sidebar-closed');

            // Simpan state ke LocalStorage (Gunakan key berbeda 'staffSidebarState')
            if (document.documentElement.classList.contains('sidebar-closed')) {
                localStorage.setItem('staffSidebarState', 'closed');
            } else {
                localStorage.setItem('staffSidebarState', 'open');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>

</html>
