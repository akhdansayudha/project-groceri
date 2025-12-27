<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- TAMBAHKAN CSRF TOKEN --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Portal - Vektora</title>

    {{-- 1. TAILWIND CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>

    {{-- 2. ALPINE JS (WAJIB UNTUK x-data, @click, x-show) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- 3. FEATHER ICONS --}}
    <script src="https://unpkg.com/feather-icons"></script>

    {{-- 4. FLATPICKR (Date Range Picker) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- Theme Dark (Opsional, agar sesuai desain Vektora) --}}
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- 5. APEXCHARTS (Untuk Grafik Analytics) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- STACK STYLES (Untuk injeksi CSS dari view spesifik) --}}
    @stack('styles')

    <style>
        /* ... style custom scrollbar tetap sama ... */
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

        /* Mencegah kedipan Alpine.js */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <div class="flex min-h-screen">

        {{-- INCLUDE SIDEBAR --}}
        @include('admin.partials.sidebar')

        {{-- MAIN CONTENT WRAPPER --}}
        <main class="flex-1 flex flex-col h-screen overflow-hidden">

            {{-- INCLUDE HEADER --}}
            @include('admin.partials.header')

            {{-- DYNAMIC CONTENT & FOOTER WRAPPER --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col">

                {{-- Content Area --}}
                <div class="flex-1 p-8">
                    @yield('content')
                </div>

                {{-- Footer Area --}}
                <div class="px-8">
                    @include('admin.partials.footer')
                </div>
            </div>

        </main>
    </div>

    {{-- Inisialisasi Feather Icons --}}
    <script>
        feather.replace();
    </script>

    {{-- STACK SCRIPTS (Untuk injeksi JS dari view spesifik) --}}
    @stack('scripts')
</body>

</html>
