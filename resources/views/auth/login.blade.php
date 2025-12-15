@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* --- CUSTOM ALERT STYLE (Vektora Theme) --- */
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

        .swal2-html-container {
            font-size: 0.95rem !important;
            color: #666 !important;
            line-height: 1.6 !important;
        }

        .swal-btn-confirm {
            background-color: #000 !important;
            color: #fff !important;
            border-radius: 9999px !important;
            padding: 12px 32px !important;
            font-weight: 600 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            transition: transform 0.2s !important;
        }

        .swal-btn-confirm:hover {
            transform: scale(0.98);
        }

        .swal2-icon.swal2-success {
            border-color: #111 !important;
            color: #111 !important;
        }

        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }
    </style>

    <div class="min-h-screen w-full flex items-center justify-center bg-[#F3F4F6] p-4 relative overflow-hidden">
        <div class="absolute top-10 left-10 text-2xl font-bold tracking-tighter z-10">
            <a href="{{ route('home') }}" class="hover-target">vektora<span class="text-blue-600">.</span></a>
        </div>

        <div class="bg-white rounded-[3rem] shadow-xl w-full max-w-7xl overflow-hidden min-h-[700px] flex relative reveal">
            <div class="hidden lg:flex w-1/2 relative overflow-hidden transition-all duration-700 bg-black" id="left-panel">
                <div id="visual-login"
                    class="absolute inset-0 flex flex-col justify-between p-16 transition-all duration-700 opacity-100 translate-x-0 z-20">
                    <div class="absolute inset-0 opacity-40">
                        <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1000&auto=format&fit=crop"
                            class="w-full h-full object-cover grayscale" alt="Visual Login">
                    </div>
                    <div class="relative z-10">
                        <span
                            class="inline-block px-4 py-2 border border-white/30 rounded-full text-xs font-bold uppercase tracking-wider mb-6 text-white">Client
                            Portal</span>
                        <h2 class="text-6xl font-bold leading-tight text-white">Welcome <br> back, <br> Explorer.</h2>
                    </div>
                </div>

                <div id="visual-register"
                    class="absolute inset-0 flex flex-col justify-between p-16 transition-all duration-700 opacity-0 -translate-x-10 z-10 bg-[#111]">
                    <div class="absolute inset-0 opacity-30">
                        <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=1000&auto=format&fit=crop"
                            class="w-full h-full object-cover grayscale" alt="Visual Register">
                    </div>
                    <div class="relative z-10">
                        <span
                            class="inline-block px-4 py-2 border border-white/30 rounded-full text-xs font-bold uppercase tracking-wider mb-6 text-white">Join
                            Us</span>
                        <h2 class="text-6xl font-bold leading-tight text-white">Start your <br> journey <br> today.</h2>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/2 p-10 md:p-20 flex flex-col justify-center relative bg-white">

                <div class="absolute top-10 right-10 flex gap-6 z-30">
                    <button id="show-login"
                        class="text-sm font-bold uppercase tracking-widest border-b-2 border-black pb-1 transition-all hover-target">Login</button>
                    <button id="show-register"
                        class="text-sm font-bold uppercase tracking-widest text-gray-400 border-b-2 border-transparent pb-1 hover:text-black transition-all hover-target">Register</button>
                </div>

                <div id="login-form"
                    class="transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] opacity-100 transform translate-x-0 relative z-20 bg-white">
                    <h3 class="text-4xl font-bold mb-2">Sign In</h3>
                    <p class="text-gray-500 mb-10">Enter your details to access your account.</p>

                    <form method="POST" action="{{ route('login.post') }}" class="space-y-6"
                        onsubmit="showLoading('btn-text-login', 'btn-loader-login')">
                        @csrf

                        <div class="group relative">
                            <input type="email" name="email" id="login-email" required placeholder=" "
                                value="{{ old('email') }}"
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Email
                                Address</label>
                        </div>

                        <div class="group relative">
                            <input type="password" name="password" id="login-password" required placeholder=" "
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium pr-10">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Password</label>
                            <button type="button"
                                class="absolute right-0 bottom-3 text-gray-400 hover:text-black hover-target"
                                onclick="togglePassword('login-password', this)">
                                <i data-feather="eye" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <label class="flex items-center gap-2 cursor-pointer hover-target">
                                <input type="checkbox" name="remember" class="accent-black w-4 h-4">
                                <span class="text-gray-500">Remember me</span>
                            </label>
                            <a href="#" class="font-bold underline hover-target">Forgot password?</a>
                        </div>

                        <button type="submit"
                            class="w-full py-4 bg-black text-white rounded-full font-bold text-lg btn-invert btn-black hover-target mt-4 relative">
                            <span id="btn-text-login">Sign In</span>
                            <span id="btn-loader-login"
                                class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>

                <div id="register-form"
                    class="absolute top-0 left-0 w-full h-full p-10 md:p-20 flex flex-col justify-center transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] opacity-0 pointer-events-none transform translate-x-20 z-10 bg-white">
                    <h3 class="text-4xl font-bold mb-2">Create Account</h3>
                    <p class="text-gray-500 mb-8">Join us to start your creative journey.</p>

                    <form method="POST" action="{{ route('register.post') }}" class="space-y-6"
                        onsubmit="showLoading('btn-text-reg', 'btn-loader-reg')">
                        @csrf

                        <div class="group relative">
                            <input type="text" name="name" id="reg-name" required placeholder=" "
                                value="{{ old('name') }}"
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Full
                                Name</label>
                        </div>

                        <div class="group relative">
                            <input type="email" name="email" id="reg-email" required placeholder=" "
                                value="{{ old('email') }}"
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Email
                                Address</label>
                        </div>

                        <div class="group relative">
                            <input type="password" name="password" id="reg-password" required placeholder=" "
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium pr-10">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Password</label>
                            <button type="button"
                                class="absolute right-0 bottom-3 text-gray-400 hover:text-black hover-target"
                                onclick="togglePassword('reg-password', this)">
                                <i data-feather="eye" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="group relative">
                            <input type="password" name="password_confirmation" id="reg-password-confirm" required
                                placeholder=" "
                                class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4 font-medium pr-10">
                            <label
                                class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Confirm
                                Password</label>

                            <button type="button"
                                class="absolute right-0 bottom-3 text-gray-400 hover:text-black hover-target"
                                onclick="togglePassword('reg-password-confirm', this)">
                                <i data-feather="eye" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <button type="submit"
                            class="w-full py-4 bg-black text-white rounded-full font-bold text-lg btn-invert btn-black hover-target mt-4 relative">
                            <span id="btn-text-reg">Sign Up</span>
                            <span id="btn-loader-reg"
                                class="hidden absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>

                        <p class="text-xs text-gray-400 mt-4 text-center">
                            By registering, you agree to our Terms & Privacy Policy.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        feather.replace();

        const btnShowLogin = document.getElementById('show-login');
        const btnShowReg = document.getElementById('show-register');
        const formLogin = document.getElementById('login-form');
        const formReg = document.getElementById('register-form');
        const visualLogin = document.getElementById('visual-login');
        const visualRegister = document.getElementById('visual-register');

        // Toggle Switch Logic (UI ONLY)
        btnShowReg.addEventListener('click', () => {
            btnShowReg.classList.add('border-black', 'text-black');
            btnShowReg.classList.remove('border-transparent', 'text-gray-400');
            btnShowLogin.classList.remove('border-black', 'text-black');
            btnShowLogin.classList.add('border-transparent', 'text-gray-400');

            formLogin.classList.add('opacity-0', 'pointer-events-none', '-translate-x-20');
            formLogin.classList.remove('opacity-100', 'translate-x-0', 'z-20');
            formReg.classList.remove('opacity-0', 'pointer-events-none', 'translate-x-20');
            formReg.classList.add('opacity-100', 'translate-x-0', 'z-20');

            visualLogin.classList.remove('opacity-100', 'translate-x-0', 'z-20');
            visualLogin.classList.add('opacity-0', 'translate-x-10', 'z-10');
            visualRegister.classList.remove('opacity-0', '-translate-x-10', 'z-10');
            visualRegister.classList.add('opacity-100', 'translate-x-0', 'z-20');
        });

        btnShowLogin.addEventListener('click', () => {
            btnShowLogin.classList.add('border-black', 'text-black');
            btnShowLogin.classList.remove('border-transparent', 'text-gray-400');
            btnShowReg.classList.remove('border-black', 'text-black');
            btnShowReg.classList.add('border-transparent', 'text-gray-400');

            formReg.classList.add('opacity-0', 'pointer-events-none', 'translate-x-20');
            formReg.classList.remove('opacity-100', 'translate-x-0', 'z-20');
            formLogin.classList.remove('opacity-0', 'pointer-events-none', '-translate-x-20');
            formLogin.classList.add('opacity-100', 'translate-x-0', 'z-20');

            visualRegister.classList.remove('opacity-100', 'translate-x-0', 'z-20');
            visualRegister.classList.add('opacity-0', '-translate-x-10', 'z-10');
            visualLogin.classList.remove('opacity-0', 'translate-x-10', 'z-10');
            visualLogin.classList.add('opacity-100', 'translate-x-0', 'z-20');
        });

        // Toggle Password Visibility
        window.togglePassword = function(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                btn.innerHTML = `<i data-feather="eye-off" class="w-5 h-5"></i>`;
            } else {
                input.type = "password";
                btn.innerHTML = `<i data-feather="eye" class="w-5 h-5"></i>`;
            }
            feather.replace();
        };

        // Loading Helper (UI Feedback sebelum submit)
        window.showLoading = function(textId, loaderId) {
            document.getElementById(textId).classList.add('invisible');
            document.getElementById(loaderId).classList.remove('hidden');
        }

        // --- SERVER SIDE ERROR HANDLING (SWEETALERT) ---
        // Script ini akan berjalan jika Laravel mengembalikan error validasi
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
                confirmButtonText: 'Check Again',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-btn-confirm'
                }
            });

            // Jika error terjadi di form Register, otomatis switch ke tab Register
            @if (old('name'))
                btnShowReg.click();
            @endif
        @endif
    </script>
@endsection
