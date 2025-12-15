@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto fade-in">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.staff.index') }}"
                class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-bold tracking-tight">Add New Team Member</h1>
        </div>

        {{-- ERROR NOTIFICATION --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3">
                <i data-feather="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-bold">There were some problems with your input:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
            <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-6" id="createForm">
                @csrf

                {{-- Full Name --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="full_name" id="fullName" required placeholder="e.g. John Doe"
                        value="{{ old('full_name') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" id="email" required placeholder="staff@vektora.com"
                        value="{{ old('email') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                {{-- Role Selection --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Role / Access Level</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="staff" class="peer sr-only" checked>
                            <div
                                class="p-4 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:bg-gray-50 hover:bg-gray-50 transition-all">
                                <div class="font-bold text-gray-900">Staff</div>
                                <div class="text-xs text-gray-500">Can view & manage assigned projects.</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="admin" class="peer sr-only">
                            <div
                                class="p-4 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:bg-black peer-checked:text-white hover:bg-gray-50 transition-all">
                                <div class="font-bold">Administrator</div>
                                <div class="text-xs opacity-70">Full access to system & finance.</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                placeholder="Create a strong password"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-black focus:bg-white transition-all">

                            {{-- EYE TOGGLE BUTTON --}}
                            <button type="button" id="togglePasswordBtn"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black transition-colors focus:outline-none">
                                <i data-feather="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Password Requirements --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Password Requirements</p>
                        <ul class="space-y-1 text-xs text-gray-500">
                            <li id="req-length" class="flex items-center gap-2 transition-colors">
                                <i data-feather="circle" class="w-3 h-3"></i> Minimum 8 characters
                            </li>
                            <li id="req-case" class="flex items-center gap-2 transition-colors">
                                <i data-feather="circle" class="w-3 h-3"></i> Mixed Case (Upper & Lower)
                            </li>
                            <li id="req-num" class="flex items-center gap-2 transition-colors">
                                <i data-feather="circle" class="w-3 h-3"></i> Contains Number (0-9)
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="pt-4">
                    {{-- Button Disabled by Default --}}
                    <button type="submit" id="submitBtn" disabled
                        class="w-full py-3 bg-black text-white rounded-xl font-bold shadow-lg shadow-black/20 hover:bg-gray-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        Create Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT VALIDASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fullName = document.getElementById('fullName');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Requirement Elements
            const reqLength = document.getElementById('req-length');
            const reqCase = document.getElementById('req-case');
            const reqNum = document.getElementById('req-num');

            // --- 1. FITUR TOGGLE PASSWORD ---
            togglePasswordBtn.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);

                // Ganti Icon (Eye vs Eye-Off)
                // Kita replace HTML icon secara manual karena feather.replace() tidak otomatis di event click
                if (type === 'text') {
                    this.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off w-5 h-5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                } else {
                    this.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye w-5 h-5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                }
            });

            // --- 2. FUNGSI UPDATE TAMPILAN SYARAT ---
            function updateRequirement(element, isValid) {
                if (isValid) {
                    element.classList.remove('text-gray-500');
                    element.classList.add('text-green-600', 'font-bold');
                    // Icon Check Hijau
                    element.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check text-green-600"><polyline points="20 6 9 17 4 12"></polyline></svg> <span class="ml-1">${element.innerText}</span>`;
                } else {
                    element.classList.remove('text-green-600', 'font-bold');
                    element.classList.add('text-gray-500');
                    // Icon Circle Abu
                    element.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg> <span class="ml-1">${element.innerText}</span>`;
                }
            }

            // --- 3. VALIDASI FORM & PASSWORD ---
            function validateForm() {
                const val = password.value;

                // Regex Checks
                const isLength = val.length >= 8;
                const isCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
                const isNum = /\d/.test(val); // Menggunakan \d untuk mendeteksi angka 0-9

                // Update UI Visuals
                updateRequirement(reqLength, isLength);
                updateRequirement(reqCase, isCase);
                updateRequirement(reqNum, isNum);

                // Check All Fields for Submit Button
                const isNameFilled = fullName.value.trim() !== '';
                const isEmailFilled = email.value.trim() !== '';
                const isPasswordValid = isLength && isCase && isNum;

                if (isNameFilled && isEmailFilled && isPasswordValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400');
                    submitBtn.classList.add('bg-black');
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Listeners
            password.addEventListener('input', validateForm);
            fullName.addEventListener('input', validateForm);
            email.addEventListener('input', validateForm);
        });
    </script>
@endsection
