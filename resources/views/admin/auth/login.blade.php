<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vektora</title>

    {{-- GANTI VITE DENGAN CDN AGAR TIDAK PERLU NPM RUN BUILD --}}
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

    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
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

<body class="bg-[#0a0a0a] text-gray-300 font-sans antialiased h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-8 fade-in">

        {{-- Logo Area --}}
        <div class="text-center mb-10">
            <div
                class="w-16 h-16 bg-white text-black rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-white/10">
                <span class="text-3xl font-bold tracking-tighter">V.</span>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Admin Portal</h1>
            <p class="text-gray-500 text-sm mt-2">Masuk untuk mengelola agency.</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-[#111] border border-gray-800 rounded-3xl p-8 shadow-2xl">

            @if ($errors->any())
                <div
                    class="bg-red-900/20 border border-red-900/50 text-red-400 px-4 py-3 rounded-xl mb-6 text-xs font-bold flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-4 h-4"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email"
                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Email
                        Address</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" required autofocus
                            class="w-full bg-[#0a0a0a] border border-gray-800 text-white rounded-xl px-4 py-3 pl-11 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-gray-700"
                            placeholder="admin@vektora.id">
                        <i data-feather="mail"
                            class="w-4 h-4 text-gray-600 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <div>
                    <label for="password"
                        class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-[#0a0a0a] border border-gray-800 text-white rounded-xl px-4 py-3 pl-11 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-gray-700"
                            placeholder="••••••••">
                        <i data-feather="lock"
                            class="w-4 h-4 text-gray-600 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="rounded bg-gray-800 border-gray-700 text-white focus:ring-0">
                        <span class="text-xs text-gray-500 font-medium">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-white text-black font-bold py-3.5 rounded-xl hover:bg-gray-200 transition-colors shadow-lg shadow-white/5 flex items-center justify-center gap-2">
                    <i data-feather="log-in" class="w-4 h-4"></i>
                    Access Dashboard
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('home') }}"
                class="text-xs font-bold text-gray-600 hover:text-white transition-colors flex items-center justify-center gap-2">
                <i data-feather="arrow-left" class="w-3 h-3"></i> Back to Homepage
            </a>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
