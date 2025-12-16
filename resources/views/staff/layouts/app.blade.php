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

    {{-- 2. ALPINE JS (INI YANG SEBELUMNYA HILANG) --}}
    {{-- Wajib ada agar x-data, @click, x-show, x-model berfungsi --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- 3. FEATHER ICONS --}}
    <script src="https://unpkg.com/feather-icons"></script>

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

        /* Tambahkan style ini untuk mencegah "fouc" (flash of unstyled content)
           Element dengan x-cloak akan disembunyikan sampai Alpine selesai loading
        */
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
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        @include('staff.partials.sidebar')

        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col h-screen overflow-hidden">

            {{-- HEADER --}}
            @include('staff.partials.header')

            {{-- CONTENT --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
                @yield('content')

                <footer class="mt-10 text-center text-xs text-gray-400 pb-4">
                    &copy; {{ date('Y') }} Vektora Creative Agency.
                </footer>
            </div>
        </main>
    </div>
    <script>
        feather.replace();
    </script>
</body>

</html>
