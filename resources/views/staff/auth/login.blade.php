<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - Vektora Agency</title>

    {{-- CDN Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        luxury: {
                            black: '#050505',
                            card: '#0F0F0F',
                            border: '#1F1F1F',
                            input: '#141414',
                            white: '#FFFFFF',
                            muted: '#666666',
                            accent: '#3B82F6' // Biru untuk nuansa produktivitas Staff
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        },
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        body {
            background-color: #000;
            color: #fff;
        }

        /* Noise Texture */
        .noise-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 50;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #141414 inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-8 relative overflow-hidden">

    <div class="noise-bg"></div>

    {{-- Background Glow Effects (Cool Blue for Staff) --}}
    <div
        class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-blue-900 rounded-full blur-[180px] opacity-[0.1]">
    </div>
    <div
        class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] bg-indigo-900 rounded-full blur-[150px] opacity-[0.1]">
    </div>

    {{-- MAIN CONTAINER (BENTO LAYOUT) --}}
    <div
        class="w-full max-w-[1200px] min-h-[600px] grid grid-cols-1 lg:grid-cols-12 gap-6 relative z-10 animate-fade-in">

        {{-- LEFT PANEL: MOTIVATION & STATS --}}
        @php
            // Mengambil Data Global Tim untuk Motivasi
            try {
                // Hitung task yang selesai bulan ini
                $completedThisMonth = \App\Models\Task::where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->count();

                // Total Staff Aktif
                $totalStaff = \App\Models\User::where('role', 'staff')->count();
            } catch (\Exception $e) {
                $completedThisMonth = 0;
                $totalStaff = 0;
            }
        @endphp

        <div
            class="lg:col-span-7 glass-panel rounded-[2.5rem] p-10 flex flex-col justify-between relative overflow-hidden group">

            {{-- Header Visual --}}
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div
                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-black font-bold text-xl tracking-tighter">
                        V.</div>
                    <span class="text-sm font-mono tracking-widest text-gray-400 uppercase">Production Hub</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold tracking-tighter leading-[1.1] mb-6">
                    Crafting <br> <span class="text-blue-500">The Future.</span>
                </h1>
                <p class="text-gray-400 max-w-md font-light leading-relaxed">
                    Collaborate, create, and deliver exceptional work. Access your workspace and track your performance
                    here.
                </p>
            </div>

            {{-- Bento Grid Stats (Global Team Stats) --}}
            <div class="grid grid-cols-2 gap-4 mt-12 relative z-10">

                {{-- Card 1: Team Velocity --}}
                <div
                    class="bg-[#0A0A0A] p-6 rounded-3xl border border-white/5 hover:border-blue-500/30 transition-all duration-500 group/card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 bg-blue-500/10 text-blue-400 rounded-full group-hover/card:bg-blue-500 group-hover/card:text-white transition-all">
                            <i data-feather="zap" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1">{{ $completedThisMonth }}</div>
                    <div class="text-xs text-gray-500 font-medium">Tasks Shipped This Month</div>
                </div>

                {{-- Card 2: Team Members --}}
                <div
                    class="bg-[#0A0A0A] p-6 rounded-3xl border border-white/5 hover:border-white/20 transition-all duration-500 group/card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 bg-white/5 rounded-full text-gray-400 group-hover/card:text-white transition-colors">
                            <i data-feather="users" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1">{{ $totalStaff }}</div>
                    <div class="text-xs text-gray-500 font-medium">Active Creators</div>
                </div>

            </div>
        </div>

        {{-- RIGHT PANEL: LOGIN FORM --}}
        <div
            class="lg:col-span-5 bg-luxury-card rounded-[2.5rem] p-10 border border-luxury-border flex flex-col justify-center relative shadow-2xl">

            <div class="absolute top-8 right-8">
                <a href="{{ route('home') }}"
                    class="text-xs font-bold text-gray-500 hover:text-white flex items-center gap-2 transition-colors">
                    Back to Home <i data-feather="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>

            <div class="mb-10 mt-6">
                <h2 class="text-2xl font-bold text-white mb-2">Staff Login</h2>
                <p class="text-gray-500 text-sm">Enter your credentials to access the workspace.</p>
            </div>

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-900/10 border border-red-900/30 flex items-start gap-3">
                    <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mt-0.5"></i>
                    <div class="text-sm text-red-400 font-medium">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('staff.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Email</label>
                    <div class="relative group">
                        <input type="email" name="email" required autofocus
                            class="w-full bg-luxury-input border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-gray-700 outline-none focus:border-blue-500/50 focus:bg-luxury-input/80 transition-all duration-300 font-medium"
                            placeholder="staff@vektora.id">
                        <div
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-white transition-colors">
                            <i data-feather="mail" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Password</label>
                    <div class="relative group">
                        <input type="password" name="password" required
                            class="w-full bg-luxury-input border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-gray-700 outline-none focus:border-blue-500/50 focus:bg-luxury-input/80 transition-all duration-300 font-medium tracking-widest"
                            placeholder="••••••••">
                        <div
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-white transition-colors">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                {{-- Remember Me Only (Forgot Password Removed) --}}
                <div class="flex items-center pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div
                                class="w-5 h-5 border border-white/20 rounded-md peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all">
                            </div>
                            <i data-feather="check"
                                class="w-3 h-3 text-white absolute top-1 left-1 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-sm text-gray-500 group-hover:text-gray-300 transition-colors">Keep me signed
                            in</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-white text-black font-bold text-lg py-4 rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(255,255,255,0.2)] mt-4 flex items-center justify-center gap-3">
                    <span>Access Workspace</span>
                    <i data-feather="arrow-right" class="w-5 h-5"></i>
                </button>

            </form>

            <div class="mt-8 pt-6 border-t border-white/5 text-center">
                <p class="text-[10px] text-gray-600 font-mono uppercase tracking-widest">
                    Authorized Personnel Only
                </p>
            </div>

        </div>

    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
