@extends('layouts.app')

@section('content')
    <div class="min-h-screen w-full flex items-center justify-center bg-[#F3F4F6] p-4 relative overflow-hidden">
        {{-- Background Blur --}}
        <div
            class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-blue-100 rounded-full blur-[150px] opacity-40 pointer-events-none">
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl w-full max-w-lg p-10 relative z-10 border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold mb-2">Set New Password</h2>
                <p class="text-gray-500 text-sm">Create a new secure password for your account.</p>
            </div>

            @if ($errors->any())
                <div
                    class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm font-bold flex items-center gap-3">
                    <i data-feather="alert-circle" class="w-5 h-5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email Readonly --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Email</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly
                        class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-5 py-4 text-gray-500 font-medium cursor-not-allowed outline-none">
                </div>

                {{-- New Password --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-black outline-none focus:border-black transition-all font-medium pr-12">
                        <button type="button"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black transition-colors focus:outline-none"
                            onclick="togglePassword('password', this)">
                            <i data-feather="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirm" required
                            placeholder="••••••••"
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-black outline-none focus:border-black transition-all font-medium pr-12">
                        <button type="button"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black transition-colors focus:outline-none"
                            onclick="togglePassword('password_confirm', this)">
                            <i data-feather="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                {{-- Password Requirements (OPTIMIZED) --}}
                <div class="bg-gray-50 rounded-2xl p-5 mt-4 border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Password Requirements</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        {{-- Req 1: Length --}}
                        <li id="req-length" class="flex items-center gap-2 transition-colors duration-300">
                            {{-- Icon Default (Gray Circle) --}}
                            <span class="icon-default block"><i data-feather="circle"
                                    class="w-4 h-4 text-gray-300 fill-gray-100"></i></span>
                            {{-- Icon Valid (Green Check) --}}
                            <span class="icon-valid hidden"><i data-feather="check-circle"
                                    class="w-4 h-4 text-green-500 fill-green-100"></i></span>
                            <span class="req-text">Minimum 8 characters</span>
                        </li>

                        {{-- Req 2: Case --}}
                        <li id="req-case" class="flex items-center gap-2 transition-colors duration-300">
                            <span class="icon-default block"><i data-feather="circle"
                                    class="w-4 h-4 text-gray-300 fill-gray-100"></i></span>
                            <span class="icon-valid hidden"><i data-feather="check-circle"
                                    class="w-4 h-4 text-green-500 fill-green-100"></i></span>
                            <span class="req-text">Uppercase & Lowercase letters</span>
                        </li>

                        {{-- Req 3: Number --}}
                        <li id="req-number" class="flex items-center gap-2 transition-colors duration-300">
                            <span class="icon-default block"><i data-feather="circle"
                                    class="w-4 h-4 text-gray-300 fill-gray-100"></i></span>
                            <span class="icon-valid hidden"><i data-feather="check-circle"
                                    class="w-4 h-4 text-green-500 fill-green-100"></i></span>
                            <span class="req-text">Contains a number</span>
                        </li>

                        {{-- Req 4: Match --}}
                        <li id="req-match" class="flex items-center gap-2 transition-colors duration-300">
                            <span class="icon-default block"><i data-feather="circle"
                                    class="w-4 h-4 text-gray-300 fill-gray-100"></i></span>
                            <span class="icon-valid hidden"><i data-feather="check-circle"
                                    class="w-4 h-4 text-green-500 fill-green-100"></i></span>
                            <span class="req-text">Passwords match</span>
                        </li>
                    </ul>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="submitBtn" disabled
                    class="w-full bg-black text-white font-bold text-lg py-4 rounded-2xl transition-all shadow-xl shadow-black/10 mt-4 disabled:opacity-50 disabled:bg-gray-400 disabled:shadow-none disabled:cursor-not-allowed hover:scale-[1.02] active:scale-[0.98]">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script>
        // Gunakan DOMContentLoaded agar script jalan SETELAH elemen HTML siap
        document.addEventListener("DOMContentLoaded", function() {
            // Init Feather Icons sekali saja di awal
            feather.replace();

            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirm');
            const submitBtn = document.getElementById('submitBtn');

            // Elements Requirements
            const reqLength = document.getElementById('req-length');
            const reqCase = document.getElementById('req-case');
            const reqNumber = document.getElementById('req-number');
            const reqMatch = document.getElementById('req-match');

            // Fungsi Toggle Style (Tanpa Render Ulang HTML)
            function updateRequirementUI(element, isValid) {
                const iconDefault = element.querySelector('.icon-default');
                const iconValid = element.querySelector('.icon-valid');
                const text = element.querySelector('.req-text');

                if (isValid) {
                    // Switch Icon
                    iconDefault.classList.add('hidden');
                    iconDefault.classList.remove('block');

                    iconValid.classList.add('block');
                    iconValid.classList.remove('hidden');

                    // Change Text Color
                    element.classList.remove('text-gray-500');
                    element.classList.add('text-green-600', 'font-bold');
                } else {
                    // Revert Icon
                    iconDefault.classList.add('block');
                    iconDefault.classList.remove('hidden');

                    iconValid.classList.add('hidden');
                    iconValid.classList.remove('block');

                    // Revert Text Color
                    element.classList.add('text-gray-500');
                    element.classList.remove('text-green-600', 'font-bold');
                }
            }

            function validateForm() {
                const val = passwordInput.value;
                const confirmVal = confirmInput.value;

                // Regex Logic
                const hasLength = val.length >= 8;
                const hasCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
                const hasNumber = /\d/.test(val);
                const isMatch = val.length > 0 && val === confirmVal;

                // Update UI
                updateRequirementUI(reqLength, hasLength);
                updateRequirementUI(reqCase, hasCase);
                updateRequirementUI(reqNumber, hasNumber);
                updateRequirementUI(reqMatch, isMatch);

                // Enable/Disable Button
                if (hasLength && hasCase && hasNumber && isMatch) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.add('bg-black', 'hover:scale-[1.02]');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.remove('bg-black', 'hover:scale-[1.02]');
                }
            }

            // Listeners
            passwordInput.addEventListener('input', validateForm);
            confirmInput.addEventListener('input', validateForm);
        });

        // Toggle Password Visibility (Global Function)
        window.togglePassword = function(inputId, btn) {
            const input = document.getElementById(inputId);

            // Cek icon svg di dalam button (karena feather replace mengubah i jadi svg)
            const iconSvg = btn.querySelector('svg');

            if (input.type === "password") {
                input.type = "text";
                // Ganti icon ke eye-off
                btn.innerHTML = `<i data-feather="eye-off" class="w-5 h-5"></i>`;
            } else {
                input.type = "password";
                // Ganti icon ke eye
                btn.innerHTML = `<i data-feather="eye" class="w-5 h-5"></i>`;
            }
            feather.replace(); // Refresh icon baru
        }
    </script>
@endsection
