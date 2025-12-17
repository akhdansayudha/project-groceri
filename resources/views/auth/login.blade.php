@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .swal2-popup {
            border-radius: 2rem !important;
            padding: 2rem !important;
            font-family: 'Manrope', sans-serif !important;
        }

        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #111 !important;
        }

        .swal-btn-confirm {
            background-color: #000 !important;
            color: #fff !important;
            border-radius: 9999px !important;
            padding: 12px 32px !important;
        }

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Transition Utilities */
        .fade-enter {
            opacity: 0;
            transform: translateX(20px);
            pointer-events: none;
        }

        .fade-enter-active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-exit {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .fade-exit-active {
            opacity: 0;
            transform: translateX(-20px);
            pointer-events: none;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <div class="min-h-screen w-full flex items-center justify-center bg-[#F3F4F6] p-4 relative overflow-hidden">

        {{-- Background Blobs --}}
        <div
            class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-blue-200 rounded-full blur-[150px] opacity-20 pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-200 rounded-full blur-[150px] opacity-20 pointer-events-none">
        </div>

        {{-- Main Card --}}
        <div
            class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-6xl overflow-hidden min-h-[700px] flex relative border border-white/50">

            {{-- LEFT PANEL (VISUAL) --}}
            <div class="hidden lg:flex w-5/12 relative overflow-hidden bg-black text-white p-12 flex-col justify-between"
                id="left-panel">
                {{-- Dynamic BG --}}
                <div id="bg-login" class="absolute inset-0 opacity-60 transition-opacity duration-700"><img
                        src="https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1000&auto=format&fit=crop"
                        class="w-full h-full object-cover grayscale"></div>
                <div id="bg-register" class="absolute inset-0 opacity-0 transition-opacity duration-700"><img
                        src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=1000&auto=format&fit=crop"
                        class="w-full h-full object-cover grayscale"></div>

                <div class="relative z-10"><a href="{{ route('home') }}"
                        class="text-3xl font-bold tracking-tighter text-white">vektora<span
                            class="text-blue-500">.</span></a></div>

                {{-- Stacked Text --}}
                <div class="relative z-10 grid grid-cols-1">
                    <div id="text-login"
                        class="col-start-1 row-start-1 transition-all duration-700 opacity-100 translate-y-0">
                        <span
                            class="inline-block px-4 py-2 border border-white/20 bg-white/10 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-6">Client
                            Portal</span>
                        <h2 class="text-5xl font-bold leading-tight mb-4">Welcome <br> back.</h2>
                        <p class="text-gray-400 font-medium">Manage your projects seamlessly.</p>
                    </div>
                    <div id="text-register"
                        class="col-start-1 row-start-1 transition-all duration-700 opacity-0 translate-y-10">
                        <span
                            class="inline-block px-4 py-2 border border-white/20 bg-white/10 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-6">Join
                            Vektora</span>
                        <h2 class="text-5xl font-bold leading-tight mb-4">Start your <br> journey.</h2>
                        <p class="text-gray-400 font-medium">Create account to kickstart projects.</p>
                    </div>
                </div>

                <div class="relative z-10 text-xs text-gray-500 font-bold uppercase tracking-widest">&copy; 2025 Vektora
                    Agency</div>
            </div>

            {{-- RIGHT PANEL (FORMS) --}}
            <div class="w-full lg:w-7/12 p-8 md:p-16 flex flex-col relative bg-white justify-center">

                {{-- Toggle Switch --}}
                <div class="absolute top-10 right-10 z-30 flex bg-gray-100 p-1 rounded-full">
                    <button id="tab-login"
                        class="px-6 py-2 rounded-full text-xs font-bold bg-white shadow-sm text-black transition-all">Login</button>
                    <button id="tab-register"
                        class="px-6 py-2 rounded-full text-xs font-bold text-gray-500 hover:text-black transition-all">Register</button>
                </div>

                {{-- FORM CONTAINER (GRID STACK - ANTI CRASH) --}}
                <div class="grid grid-cols-1 relative w-full max-w-md mx-auto">

                    {{-- 1. LOGIN FORM --}}
                    <div id="login-container"
                        class="col-start-1 row-start-1 transition-all duration-500 ease-in-out opacity-100 translate-x-0 z-10">
                        <div class="mb-10">
                            <h3 class="text-3xl font-bold mb-3 tracking-tight">Sign In</h3>
                            <p class="text-gray-500 text-sm">Enter your credentials to access dashboard.</p>
                        </div>

                        <form method="POST" action="{{ route('login.post') }}" class="space-y-6"
                            onsubmit="showLoading('btn-text-login', 'btn-loader-login')">
                            @csrf
                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-1">Email</label>
                                <input type="email" name="email" required placeholder="name@example.com"
                                    value="{{ old('email') }}"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-black focus:bg-white transition-all font-medium">
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center ml-1">
                                    <label
                                        class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Password</label>
                                    <a href="{{ route('password.request') }}"
                                        class="text-[10px] font-bold text-blue-600 hover:underline">Forgot Password?</a>
                                </div>
                                <div class="relative">
                                    <input type="password" name="password" id="login-password" required
                                        placeholder="••••••••"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-black focus:bg-white transition-all font-medium pr-10">
                                    <button type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black"
                                        onclick="togglePassword('login-password', this)">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center pt-2">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="remember" class="accent-black w-4 h-4">
                                    <span class="text-xs text-gray-500 font-medium">Remember me</span>
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full py-3.5 bg-black text-white rounded-xl font-bold text-sm hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg mt-4 relative">
                                <span id="btn-text-login">Sign In</span>
                                <span id="btn-loader-login"
                                    class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    </div>

                    {{-- 2. REGISTER FORM --}}
                    <div id="register-container"
                        class="col-start-1 row-start-1 transition-all duration-500 ease-in-out opacity-0 translate-x-10 pointer-events-none z-0">
                        <div class="mb-6">
                            <h3 class="text-3xl font-bold mb-2 tracking-tight">Create Account</h3>
                            <p class="text-gray-500 text-sm">Join us to start your creative journey.</p>
                        </div>

                        {{-- GRID FORM LAYOUT --}}
                        <form method="POST" action="{{ route('register.post') }}" class="grid grid-cols-2 gap-4"
                            onsubmit="showLoading('btn-text-reg', 'btn-loader-reg')">
                            @csrf

                            {{-- Row 1: Full Name (Col Span 2) --}}
                            <div class="col-span-2 space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-1">Full
                                    Name</label>
                                <input type="text" name="name" id="reg-name" required value="{{ old('name') }}"
                                    placeholder="John Doe"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-black outline-none focus:border-black focus:bg-white transition-all font-medium">
                            </div>

                            {{-- Row 2: Email (Col Span 2) --}}
                            <div class="col-span-2 space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-1">Email
                                    Address</label>
                                <input type="email" name="email" id="reg-email" required value="{{ old('email') }}"
                                    placeholder="name@company.com"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-black outline-none focus:border-black focus:bg-white transition-all font-medium">
                            </div>

                            {{-- Row 3: Password (Left) --}}
                            <div class="col-span-1 space-y-1">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-1">Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="reg-password" required
                                        placeholder="Create"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-black outline-none focus:border-black focus:bg-white transition-all font-medium pr-8">
                                    <button type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black"
                                        onclick="togglePassword('reg-password', this)">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Row 3: Confirm (Right) --}}
                            <div class="col-span-1 space-y-1">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-1">Confirm</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="reg-password-confirm"
                                        required placeholder="Repeat"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-black outline-none focus:border-black focus:bg-white transition-all font-medium pr-8">
                                    <button type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black"
                                        onclick="togglePassword('reg-password-confirm', this)">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Row 4: Requirements (Full Width) --}}
                            <div class="col-span-2">
                                <div id="password-requirements"
                                    class="bg-gray-50 rounded-xl p-3 border border-gray-100 hidden transition-all">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password
                                        must contain:</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <ul class="space-y-1 text-[10px] text-gray-500">
                                            <li id="req-length" class="flex items-center gap-1.5"><i
                                                    data-feather="circle" class="w-3 h-3"></i> Min. 8 chars</li>
                                            <li id="req-case" class="flex items-center gap-1.5"><i data-feather="circle"
                                                    class="w-3 h-3"></i> Upper & Lower</li>
                                        </ul>
                                        <ul class="space-y-1 text-[10px] text-gray-500">
                                            <li id="req-number" class="flex items-center gap-1.5"><i
                                                    data-feather="circle" class="w-3 h-3"></i> Number (0-9)</li>
                                            <li id="req-match" class="flex items-center gap-1.5"><i data-feather="circle"
                                                    class="w-3 h-3"></i> Passwords match</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 5: Submit --}}
                            <div class="col-span-2 mt-2">
                                <button type="submit" id="btn-register-submit" disabled
                                    class="w-full py-3.5 bg-black text-white rounded-xl font-bold text-sm shadow-lg relative disabled:opacity-50 disabled:cursor-not-allowed hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    <span id="btn-text-reg">Create Account</span>
                                    <span id="btn-loader-reg"
                                        class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>
                                <p class="text-[10px] text-gray-400 text-center mt-3">By registering, you agree to Terms &
                                    Privacy.</p>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();

            // --- VARIABLES ---
            const btnLogin = document.getElementById('tab-login');
            const btnRegister = document.getElementById('tab-register');
            const containerLogin = document.getElementById('login-container');
            const containerRegister = document.getElementById('register-container');
            const bgLogin = document.getElementById('bg-login');
            const bgRegister = document.getElementById('bg-register');
            const textLogin = document.getElementById('text-login');
            const textRegister = document.getElementById('text-register');

            // --- SWITCH LOGIC ---
            function toggleTabs(isRegister) {
                if (isRegister) {
                    // Button State
                    btnRegister.className =
                        "px-6 py-2 rounded-full text-xs font-bold bg-white shadow-sm text-black transition-all";
                    btnLogin.className =
                        "px-6 py-2 rounded-full text-xs font-bold text-gray-500 hover:text-black transition-all";

                    // Container Transition (CSS Grid Stack)
                    containerLogin.classList.remove('opacity-100', 'z-10');
                    containerLogin.classList.add('opacity-0', '-translate-x-10', 'pointer-events-none', 'z-0');

                    containerRegister.classList.remove('opacity-0', 'translate-x-10', 'pointer-events-none', 'z-0');
                    containerRegister.classList.add('opacity-100', 'translate-x-0', 'z-10');

                    // Visual Panel
                    bgLogin.classList.replace('opacity-60', 'opacity-0');
                    bgRegister.classList.replace('opacity-0', 'opacity-60');
                    textLogin.classList.replace('opacity-100', 'opacity-0');
                    textLogin.classList.replace('translate-y-0', '-translate-y-10');
                    textRegister.classList.replace('opacity-0', 'opacity-100');
                    textRegister.classList.replace('translate-y-10', 'translate-y-0');

                } else {
                    // Button State
                    btnLogin.className =
                        "px-6 py-2 rounded-full text-xs font-bold bg-white shadow-sm text-black transition-all";
                    btnRegister.className =
                        "px-6 py-2 rounded-full text-xs font-bold text-gray-500 hover:text-black transition-all";

                    // Container Transition
                    containerRegister.classList.remove('opacity-100', 'z-10');
                    containerRegister.classList.add('opacity-0', 'translate-x-10', 'pointer-events-none', 'z-0');

                    containerLogin.classList.remove('opacity-0', '-translate-x-10', 'pointer-events-none', 'z-0');
                    containerLogin.classList.add('opacity-100', 'translate-x-0', 'z-10');

                    // Visual Panel
                    bgRegister.classList.replace('opacity-60', 'opacity-0');
                    bgLogin.classList.replace('opacity-0', 'opacity-60');
                    textRegister.classList.replace('opacity-100', 'opacity-0');
                    textRegister.classList.replace('translate-y-0', 'translate-y-10');
                    textLogin.classList.replace('opacity-0', 'opacity-100');
                    textLogin.classList.replace('-translate-y-10', 'translate-y-0');
                }
            }

            btnRegister.addEventListener('click', () => toggleTabs(true));
            btnLogin.addEventListener('click', () => toggleTabs(false));

            // --- PASSWORD VALIDATION ---
            const passInput = document.getElementById('reg-password');
            const confirmInput = document.getElementById('reg-password-confirm');
            const reqBox = document.getElementById('password-requirements');
            const btnSubmit = document.getElementById('btn-register-submit');

            const reqs = {
                length: document.getElementById('req-length'),
                case: document.getElementById('req-case'),
                number: document.getElementById('req-number'),
                match: document.getElementById('req-match'),
            };

            function updateReqUI(element, isValid) {
                if (isValid) {
                    element.classList.remove('text-gray-500');
                    element.classList.add('text-green-600', 'font-bold');
                    element.querySelector('svg').outerHTML =
                        `<i data-feather="check-circle" class="w-3 h-3 text-green-500"></i>`;
                } else {
                    element.classList.add('text-gray-500');
                    element.classList.remove('text-green-600', 'font-bold');
                    element.querySelector('svg').outerHTML =
                        `<i data-feather="circle" class="w-3 h-3 text-gray-300"></i>`;
                }
                feather.replace();
            }

            function validatePassword() {
                const val = passInput.value;
                const confirmVal = confirmInput.value;

                if (val.length > 0) reqBox.classList.remove('hidden');
                else reqBox.classList.add('hidden');

                const checks = {
                    length: val.length >= 8,
                    case: /[a-z]/.test(val) && /[A-Z]/.test(val),
                    number: /\d/.test(val),
                    match: val.length > 0 && val === confirmVal
                };

                updateReqUI(reqs.length, checks.length);
                updateReqUI(reqs.case, checks.case);
                updateReqUI(reqs.number, checks.number);
                updateReqUI(reqs.match, checks.match);

                const allValid = Object.values(checks).every(Boolean);
                btnSubmit.disabled = !allValid;

                if (allValid) {
                    btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnSubmit.classList.add('hover:scale-[1.02]');
                } else {
                    btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                    btnSubmit.classList.remove('hover:scale-[1.02]');
                }
            }

            passInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', validatePassword);

            // --- ERROR HANDLING ---
            @if ($errors->any())
                let errorHtml = '<ul style="text-align: left; margin-left: 1rem;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorHtml,
                    confirmButtonText: 'Try Again',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-btn-confirm'
                    }
                });

                @if (old('name')) // Keep register tab open if error
                    toggleTabs(true);
                @endif
            @endif
        });

        window.togglePassword = function(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                btn.innerHTML = `<i data-feather="eye-off" class="w-4 h-4"></i>`;
            } else {
                input.type = "password";
                btn.innerHTML = `<i data-feather="eye" class="w-4 h-4"></i>`;
            }
            feather.replace();
        };

        window.showLoading = function(textId, loaderId) {
            document.getElementById(textId).classList.add('invisible');
            document.getElementById(loaderId).classList.remove('hidden');
        }
    </script>
@endsection
