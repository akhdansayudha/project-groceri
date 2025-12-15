<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Vektora Creative Agency</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        // Menggunakan Skema Warna Minimalis untuk Staff
                        bento: {
                            dark: '#0a0a0a',
                            card: '#111111',
                            // Accent warna hitam atau abu-abu gelap
                            accent: '#1f2937', // Dark Gray / Slate
                            'accent-light': '#374151', // Lighter Slate
                            'white-primary': '#ffffff',
                            'white-secondary': '#e5e7eb',
                        }
                    },
                    borderRadius: {
                        'bento': '1.5rem',
                        'bento-sm': '1rem',
                        'bento-lg': '2rem',
                    }
                }
            }
        }
    </script>

    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        /* Utility Styles dari Admin Login */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .slide-in-left {
            animation: slideInLeft 0.7s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.7s ease-out 0.2s both;
        }

        /* Custom Bento Grid Background (Grid berwarna putih tipis) */
        .bento-grid {
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Pulse menggunakan warna White-Primary */
        .pulse-accent {
            animation: pulse 2s infinite;
        }

        /* Hover Lift menggunakan warna Black/Dark */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Mengubah Pulse ke tema gelap/putih */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.1);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .split-layout {
                flex-direction: column;
            }

            .left-panel {
                padding: 3rem 1.5rem !important;
            }

            .right-panel {
                padding: 2rem 1.5rem !important;
            }
        }
    </style>
</head>

<body class="bg-bento-dark text-gray-300 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-6xl bg-bento-card rounded-bento-lg overflow-hidden shadow-2xl flex split-layout fade-in">

        {{-- Left Panel: Staff Showcase & Mission --}}
        <div
            class="w-1/2 bg-gradient-to-br from-bento-dark to-gray-900 p-12 flex flex-col justify-between relative overflow-hidden left-panel slide-in-left">
            {{-- Bento Grid Background --}}
            <div class="absolute inset-0 bento-grid opacity-20"></div>

            {{-- Decorative Elements --}}
            <div
                class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-gradient-to-br from-bento-white-primary to-transparent opacity-10">
            </div>
            <div
                class="absolute bottom-10 left-10 w-32 h-32 rounded-bento bg-gradient-to-tr from-bento-white-primary to-transparent opacity-5">
            </div>

            {{-- Content --}}
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div
                        class="w-12 h-12 bg-bento-white-primary text-black rounded-bento-sm flex items-center justify-center shadow-lg">
                        <i data-feather="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Vektora</h1>
                        <p class="text-xs text-gray-500">Staff Portal</p>
                    </div>
                </div>

                <h2 class="text-3xl font-bold text-white mb-6 leading-tight">
                    Your hub for <span class="text-bento-white-secondary">Production</span><br>
                    and <span class="text-bento-white-secondary">Innovation</span>
                </h2>

                <p class="text-gray-400 mb-8 max-w-md">
                    Access your assigned tasks, monitor client chats, track your performance, and deliver excellence.
                    Your work defines Vektora.
                </p>

                {{-- Bento Stats (Akan diisi dengan Live Data Staff Nanti) --}}
                <div class="grid grid-cols-3 gap-4 mb-10">
                    <div class="bg-black/40 backdrop-blur-sm rounded-bento-sm p-4 text-center border border-gray-800">
                        <div class="text-2xl font-bold text-white">4</div>
                        <div class="text-xs text-gray-500">Active Tasks</div>
                    </div>
                    <div class="bg-black/40 backdrop-blur-sm rounded-bento-sm p-4 text-center border border-gray-800">
                        <div class="text-2xl font-bold text-white">85</div>
                        <div class="text-xs text-gray-500">Toratix Earned</div>
                    </div>
                    <div class="bg-black/40 backdrop-blur-sm rounded-bento-sm p-4 text-center border border-gray-800">
                        <div class="text-2xl font-bold text-white">4.8</div>
                        <div class="text-xs text-gray-500">Rating</div>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="relative z-10">
                <div class="flex items-center gap-3 text-gray-500 text-sm">
                    <i data-feather="target" class="w-4 h-4"></i>
                    <span>Focus on completion, we handle the administration.</span>
                </div>
            </div>
        </div>

        {{-- Right Panel: Login Form --}}
        <div class="w-1/2 p-12 flex flex-col justify-center right-panel slide-in-right">
            {{-- Login Header --}}
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-white mb-2">Staff Access</h1>
                <p class="text-gray-500">Sign in with your team credentials</p>
            </div>

            {{-- Error Message --}}
            @if ($errors->any())
                <div
                    class="bg-red-900/20 border border-red-900/50 text-red-400 px-5 py-4 rounded-bento-sm mb-6 text-sm font-medium flex items-center gap-3">
                    <i data-feather="alert-circle" class="w-5 h-5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Login Card --}}
            <div class="bg-bento-dark border border-gray-800 rounded-bento p-8 hover-lift">
                <form action="{{ route('staff.login.post') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Email Field --}}
                    <div>
                        <label for="email"
                            class="block text-sm font-semibold text-gray-400 mb-2 flex items-center gap-2">
                            <i data-feather="user" class="w-4 h-4"></i>
                            Email Address
                        </label>
                        <div class="relative">
                            <input type="email" name="email" id="email" required autofocus
                                class="w-full bg-black/50 border border-gray-800 text-white rounded-bento-sm px-5 py-4 pl-12 focus:outline-none focus:border-bento-white-primary focus:ring-2 focus:ring-bento-white-primary/30 transition-all placeholder-gray-600"
                                placeholder="staff@vektora.id">
                            <i data-feather="mail"
                                class="w-5 h-5 text-gray-600 absolute left-5 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>

                    {{-- Password Field --}}
                    <div>
                        <label for="password"
                            class="block text-sm font-semibold text-gray-400 mb-2 flex items-center gap-2">
                            <i data-feather="key" class="w-4 h-4"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full bg-black/50 border border-gray-800 text-white rounded-bento-sm px-5 py-4 pl-12 focus:outline-none focus:border-bento-white-primary focus:ring-2 focus:ring-bento-white-primary/30 transition-all placeholder-gray-600"
                                placeholder="••••••••">
                            <i data-feather="lock"
                                class="w-5 h-5 text-gray-600 absolute left-5 top-1/2 -translate-y-1/2"></i>
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password (Disertakan untuk kelengkapan) --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="remember" class="sr-only peer">
                                <div
                                    class="w-5 h-5 bg-black/50 border border-gray-700 rounded-md peer-checked:bg-bento-accent-light peer-checked:border-bento-accent-light flex items-center justify-center transition-colors">
                                    <i data-feather="check" class="w-3 h-3 text-white hidden peer-checked:block"></i>
                                </div>
                            </div>
                            <span class="text-sm text-gray-400">Remember me</span>
                        </label>

                        <a href="#"
                            class="text-sm text-bento-white-secondary hover:text-white transition-colors font-medium">
                            Forgot password?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-bento-accent to-bento-accent-light text-bento-white-primary font-bold py-4 rounded-bento-sm hover:opacity-90 transition-all shadow-lg shadow-bento-accent/20 pulse-accent flex items-center justify-center gap-3 mt-8">
                        <i data-feather="log-in" class="w-5 h-5"></i>
                        Access Staff Portal
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center my-8">
                    <div class="flex-1 h-px bg-gray-800"></div>
                    <span class="px-4 text-sm text-gray-600">Secure Sign In</span>
                    <div class="flex-1 h-px bg-gray-800"></div>
                </div>

                {{-- Social Login Options --}}
                <div class="text-center">
                    <p class="text-sm text-gray-500">Internal Use Only</p>
                </div>
            </div>

            {{-- Back to Home Link --}}
            <div class="text-center mt-10">
                <a href="{{ route('home') }}"
                    class="text-gray-500 hover:text-white transition-colors text-sm font-medium flex items-center justify-center gap-2">
                    <i data-feather="arrow-left" class="w-4 h-4"></i>
                    Back to Homepage
                </a>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');

            inputs.forEach(input => {
                // Add focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-bento-white-primary/20');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-bento-white-primary/20');
                });

                // Add validation styling
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.add('border-bento-white-primary/50');
                    } else {
                        this.classList.remove('border-bento-white-primary/50');
                    }
                });
            });

            // Add hover effect to login card
            const loginCard = document.querySelector('.hover-lift');
            if (loginCard) {
                loginCard.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });

                loginCard.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            }
        });
    </script>
</body>

</html>
