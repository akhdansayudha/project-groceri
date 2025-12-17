<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Tower - Vektora Agency</title>

    {{-- CDN Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'], // Menggunakan font Manrope agar konsisten
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        luxury: {
                            black: '#050505',
                            card: '#0F0F0F',
                            border: '#1F1F1F',
                            input: '#141414',
                            white: '#FFFFFF',
                            muted: '#666666'
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

        /* Noise Texture Overlay */
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

    {{-- Background Glow Effects --}}
    <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-white rounded-full blur-[180px] opacity-[0.03]">
    </div>
    <div
        class="absolute bottom-[-20%] right-[-10%] w-[500px] h-[500px] bg-gray-500 rounded-full blur-[150px] opacity-[0.05]">
    </div>

    {{-- MAIN CONTAINER (BENTO LAYOUT) --}}
    <div
        class="w-full max-w-[1400px] min-h-[700px] grid grid-cols-1 lg:grid-cols-12 gap-6 relative z-10 animate-fade-in">

        {{-- LEFT PANEL: STATS & VISUALS (REALTIME DATA) --}}
        @php
            // MENGAMBIL DATA REALTIME DARI DATABASE
            // Menggunakan try-catch agar jika tabel belum ada tidak error fatal
            try {
                $totalProjects = \App\Models\Task::count();
                $activeClients = \App\Models\User::where('role', 'client')->count();
                $totalWorkspaces = \App\Models\Workspace::count();
                $pendingTasks = \App\Models\Task::where('status', 'queue')->count();
            } catch (\Exception $e) {
                $totalProjects = 0;
                $activeClients = 0;
                $totalWorkspaces = 0;
                $pendingTasks = 0;
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
                    <span class="text-sm font-mono tracking-widest text-gray-400 uppercase">Control Tower</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-bold tracking-tighter leading-[0.9] mb-4">
                    Orchestrate <br> <span class="text-gray-500">Excellence.</span>
                </h1>
                <p class="text-gray-400 max-w-md font-light">
                    Welcome back, Admin. Your central command for managing Vektora's creative operations.
                </p>
            </div>

            {{-- Bento Grid Stats (Realtime) --}}
            <div class="grid grid-cols-2 gap-4 mt-12 relative z-10">

                {{-- Card 1: Projects --}}
                <div
                    class="bg-[#0A0A0A] p-6 rounded-3xl border border-white/5 hover:border-white/20 transition-all duration-500 group/card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 bg-white/5 rounded-full text-gray-400 group-hover/card:text-white transition-colors">
                            <i data-feather="layers" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] uppercase tracking-widest text-gray-500">All Time</span>
                    </div>
                    <div class="text-4xl font-bold text-white mb-1">{{ number_format($totalProjects) }}</div>
                    <div class="text-xs text-gray-500 font-medium">Projects Delivered</div>
                </div>

                {{-- Card 2: Pending (Attention) --}}
                <div
                    class="bg-white text-black p-6 rounded-3xl border border-white transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-24 h-24 bg-gray-200 rounded-full blur-2xl opacity-50 -mr-5 -mt-5">
                    </div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="p-2 bg-black/10 rounded-full text-black">
                            <i data-feather="clock" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] uppercase tracking-widest text-gray-600 font-bold">Action Needed</span>
                    </div>
                    <div class="text-4xl font-bold text-black mb-1 relative z-10">{{ $pendingTasks }}</div>
                    <div class="text-xs text-gray-600 font-bold relative z-10">Pending Requests</div>
                </div>

                {{-- Card 3: Clients --}}
                <div
                    class="bg-[#0A0A0A] p-6 rounded-3xl border border-white/5 hover:border-white/20 transition-all duration-500 group/card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 bg-white/5 rounded-full text-gray-400 group-hover/card:text-white transition-colors">
                            <i data-feather="users" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-white mb-1">{{ number_format($activeClients) }}</div>
                    <div class="text-xs text-gray-500 font-medium">Active Clients</div>
                </div>

                {{-- Card 4: Workspaces --}}
                <div
                    class="bg-[#0A0A0A] p-6 rounded-3xl border border-white/5 hover:border-white/20 transition-all duration-500 group/card">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 bg-white/5 rounded-full text-gray-400 group-hover/card:text-white transition-colors">
                            <i data-feather="grid" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-white mb-1">{{ number_format($totalWorkspaces) }}</div>
                    <div class="text-xs text-gray-500 font-medium">Workspaces</div>
                </div>

            </div>

            {{-- Decorative Gradient --}}
            <div
                class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-black to-transparent opacity-80 pointer-events-none">
            </div>
        </div>

        {{-- RIGHT PANEL: LOGIN FORM --}}
        <div
            class="lg:col-span-5 bg-luxury-card rounded-[2.5rem] p-10 md:p-14 border border-luxury-border flex flex-col justify-center relative shadow-2xl">

            <div class="absolute top-10 right-10">
                <a href="{{ route('home') }}"
                    class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-white hover:text-black transition-all duration-300 group"
                    title="Back to Home">
                    <i data-feather="arrow-up-right"
                        class="w-5 h-5 group-hover:rotate-45 transition-transform duration-300"></i>
                </a>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-white mb-2">Identify Yourself.</h2>
                <p class="text-gray-500 text-sm">Access to admin panel is restricted.</p>
            </div>

            {{-- Error Alerts --}}
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-white/5 border border-red-900/30 flex items-start gap-3">
                    <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mt-0.5"></i>
                    <div class="text-sm text-red-400">
                        <span class="font-bold block mb-1">Access Denied</span>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Email</label>
                    <div class="relative group">
                        <input type="email" name="email" required autofocus
                            class="w-full bg-luxury-input border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-gray-700 outline-none focus:border-white/40 focus:bg-luxury-input/80 transition-all duration-300 font-medium"
                            placeholder="admin@vektora.id">
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
                            class="w-full bg-luxury-input border border-white/10 rounded-2xl px-5 py-4 text-white placeholder-gray-700 outline-none focus:border-white/40 focus:bg-luxury-input/80 transition-all duration-300 font-medium tracking-widest"
                            placeholder="••••••••">
                        <div
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-white transition-colors">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div
                                class="w-5 h-5 border border-white/20 rounded-md peer-checked:bg-white peer-checked:border-white transition-all">
                            </div>
                            <i data-feather="check"
                                class="w-3 h-3 text-black absolute top-1 left-1 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-sm text-gray-500 group-hover:text-gray-300 transition-colors">Remember
                            session</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-white text-black font-bold text-lg py-4 rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(255,255,255,0.2)] mt-4 flex items-center justify-center gap-3">
                    <span>Enter Dashboard</span>
                    <i data-feather="arrow-right" class="w-5 h-5"></i>
                </button>

            </form>

            <div class="mt-12 pt-8 border-t border-white/5 text-center">
                <p class="text-xs text-gray-600 font-mono">
                    SECURE SYSTEM • ENCRYPTED CONNECTION
                </p>
            </div>

        </div>

    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
