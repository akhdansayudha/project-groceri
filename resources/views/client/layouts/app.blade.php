<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Portal - Vektora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>

    {{-- 1. SCRIPT ANTI-KILAT (WAJIB DI HEAD) --}}
    <script>
        // Cek LocalStorage SEBELUM halaman dirender agar tidak ada efek kedip
        if (localStorage.getItem('sidebarState') === 'closed') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>

    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }

        /* --- LOGIKA CSS SIDEBAR (Mencegah FOUC) --- */
        /* Default: Sidebar Terbuka (dihandle Tailwind w-72) */

        /* Jika class 'sidebar-closed' ada di HTML, paksa lebar jadi 0 */
        html.sidebar-closed #sidebar-wrapper {
            width: 0 !important;
            border-right: none !important;
        }

        /* Mencegah animasi transisi saat halaman baru dimuat */
        .preload * {
            transition: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
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
    </style>
</head>

{{-- Tambahkan class 'preload' agar tidak ada animasi geser saat refresh --}}

<body class="bg-[#F8F9FB] text-[#111] antialiased preload">
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR WRAPPER --}}
        {{-- Kita biarkan default w-72, nanti CSS di head yang akan menimpanya jika closed --}}
        <div id="sidebar-wrapper"
            class="w-72 transition-all duration-300 ease-in-out overflow-hidden flex-shrink-0 border-r border-gray-200 bg-white">
            <div class="w-72">
                @include('client.partials.sidebar')
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden h-full transition-all duration-300">

            @include('client.partials.header')

            <main class="w-full flex-grow p-8 fade-in">
                @yield('content')
            </main>

            <div class="mt-auto">
                @include('client.partials.footer')
            </div>

        </div>
    </div>

    <script>
        feather.replace();

        // --- 2. HAPUS CLASS PRELOAD SETELAH LOAD ---
        // Agar animasi transisi aktif kembali setelah halaman tampil
        window.addEventListener('load', () => {
            document.body.classList.remove('preload');
        });

        // --- 3. LOGIC TOGGLE BARU ---
        const toggleBtn = document.getElementById('sidebar-toggle');

        function toggleSidebar() {
            // Kita hanya perlu toggle class di tag <html>
            // CSS di <head> yang akan mengurus perubahan lebarnya
            document.documentElement.classList.toggle('sidebar-closed');

            // Simpan state terbaru
            if (document.documentElement.classList.contains('sidebar-closed')) {
                localStorage.setItem('sidebarState', 'closed');
            } else {
                localStorage.setItem('sidebarState', 'open');
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
