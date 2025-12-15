@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto fade-in">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('admin.staff.index') }}"
                class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-bold tracking-tight">Edit Member</h1>
        </div>

        {{-- ERROR NOTIFICATION --}}
        @if ($errors->any())
            <div
                class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3 fade-in">
                <i data-feather="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
                <div class="text-sm">
                    <p class="font-bold">Update gagal:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
            <form action="{{ route('admin.staff.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Full Name --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="full_name" id="fullName" required
                        value="{{ old('full_name', $user->full_name) }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black focus:bg-white transition-all">
                </div>

                {{-- Role Selection --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Role / Access Level</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="staff" class="peer sr-only"
                                {{ $user->role == 'staff' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:bg-gray-50 hover:bg-gray-50 transition-all">
                                <div class="font-bold text-gray-900">Staff</div>
                                <div class="text-xs text-gray-500">Can view & manage assigned projects.</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="admin" class="peer sr-only"
                                {{ $user->role == 'admin' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl border border-gray-200 peer-checked:border-black peer-checked:bg-black peer-checked:text-white hover:bg-gray-50 transition-all">
                                <div class="font-bold">Administrator</div>
                                <div class="text-xs opacity-70">Full access to system & finance.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- CURRENT PASSWORD DISPLAY (DUMMY) --}}
                {{-- User meminta untuk melihat password, tapi karena hash, kita tampilkan placeholder dengan mata --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Current Password
                        (Encrypted)</label>
                    <div class="relative">
                        <input type="password" id="currentPassDisplay" disabled value="EncryptedPassword123"
                            class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed pr-12">

                        {{-- Tombol Mata untuk Current Password --}}
                        <button type="button" id="toggleCurrentPass"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black focus:outline-none">
                            <i data-feather="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                        <i data-feather="lock" class="w-3 h-3"></i>
                        Password tersimpan dalam format hash aman. Tidak dapat dilihat, hanya bisa direset.
                    </p>
                </div>

                {{-- CHANGE PASSWORD CHECKBOX --}}
                <div class="pt-2">
                    <label
                        class="flex items-center gap-3 cursor-pointer mb-4 p-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors select-none">
                        <input type="checkbox" id="togglePasswordCheckbox"
                            class="w-5 h-5 rounded border-gray-300 text-black focus:ring-black">
                        <span class="text-sm font-bold text-gray-900">Change / Reset Password</span>
                    </label>

                    {{-- NEW PASSWORD FORM (HIDDEN INITIALLY) --}}
                    <div id="newPasswordContainer"
                        class="hidden space-y-3 bg-gray-50 p-6 rounded-2xl border border-gray-200 transition-all">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">New Password</label>

                            {{-- Relative Container agar icon mata tidak lari --}}
                            <div class="relative w-full">
                                <input type="password" name="password" id="newPasswordInput"
                                    placeholder="Enter new password"
                                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-black transition-all">

                                {{-- Tombol Mata New Password (Fixed Position) --}}
                                <button type="button" id="toggleNewPassBtn"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black focus:outline-none z-10 p-1">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Requirements --}}
                        <div class="p-4 bg-white rounded-xl border border-gray-200">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Password Requirements</p>
                            <ul class="space-y-1 text-xs text-gray-500">
                                <li id="req-length" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Minimum 8 characters
                                </li>
                                <li id="req-case" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Mixed Case (Upper & Lower)
                                </li>
                                <li id="req-num" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Contains Number (0-9)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit" id="submitBtn"
                        class="flex-1 py-3 bg-black text-white rounded-xl font-bold shadow-lg shadow-black/20 hover:bg-gray-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT VALIDASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const toggleCheckbox = document.getElementById('togglePasswordCheckbox');
            const newPasswordContainer = document.getElementById('newPasswordContainer');
            const newPasswordInput = document.getElementById('newPasswordInput');

            const submitBtn = document.getElementById('submitBtn');
            const fullName = document.getElementById('fullName');
            const email = document.getElementById('email');

            // Eye Buttons
            const toggleCurrentPassBtn = document.getElementById('toggleCurrentPass');
            const currentPassDisplay = document.getElementById('currentPassDisplay');
            const toggleNewPassBtn = document.getElementById('toggleNewPassBtn');

            // Requirement Elements
            const reqLength = document.getElementById('req-length');
            const reqCase = document.getElementById('req-case');
            const reqNum = document.getElementById('req-num');

            // --- 1. TOGGLE CURRENT PASSWORD (DUMMY) ---
            toggleCurrentPassBtn.addEventListener('click', function() {
                const type = currentPassDisplay.getAttribute('type') === 'password' ? 'text' : 'password';
                currentPassDisplay.setAttribute('type', type);
                updateEyeIcon(this, type);
            });

            // --- 2. TOGGLE NEW PASSWORD ---
            toggleNewPassBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah form submit tidak sengaja
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                updateEyeIcon(this, type);
            });

            function updateEyeIcon(btn, type) {
                if (type === 'text') {
                    btn.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off w-5 h-5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                } else {
                    btn.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye w-5 h-5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                }
            }

            // --- 3. TOGGLE CHECKBOX (SHOW/HIDE FORM) ---
            toggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    newPasswordContainer.classList.remove('hidden');
                    newPasswordInput.setAttribute('required', 'required');
                    validateForm(); // Cek validasi saat dibuka
                } else {
                    newPasswordContainer.classList.add('hidden');
                    newPasswordInput.value = ''; // Reset value
                    newPasswordInput.removeAttribute('required');
                    resetRequirements(); // Reset tampilan syarat
                    validateForm(); // Cek validasi ulang (abaikan password)
                }
            });

            // --- 4. VALIDASI ---
            function updateRequirement(element, isValid) {
                if (isValid) {
                    element.classList.remove('text-gray-500');
                    element.classList.add('text-green-600', 'font-bold');
                    element.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check text-green-600"><polyline points="20 6 9 17 4 12"></polyline></svg> <span class="ml-1">${element.innerText}</span>`;
                } else {
                    element.classList.remove('text-green-600', 'font-bold');
                    element.classList.add('text-gray-500');
                    element.innerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg> <span class="ml-1">${element.innerText}</span>`;
                }
            }

            function resetRequirements() {
                // Reset text elemen requirement ke kondisi awal (tanpa icon check/circle)
                reqLength.innerHTML =
                    `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg> <span class="ml-1">Minimum 8 characters</span>`;
                reqLength.classList.remove('text-green-600', 'font-bold');
                reqLength.classList.add('text-gray-500');

                reqCase.innerHTML =
                    `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg> <span class="ml-1">Mixed Case (Upper & Lower)</span>`;
                reqCase.classList.remove('text-green-600', 'font-bold');
                reqCase.classList.add('text-gray-500');

                reqNum.innerHTML =
                    `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg> <span class="ml-1">Contains Number (0-9)</span>`;
                reqNum.classList.remove('text-green-600', 'font-bold');
                reqNum.classList.add('text-gray-500');
            }

            function validateForm() {
                const isNameFilled = fullName.value.trim() !== '';
                const isEmailFilled = email.value.trim() !== '';
                let isPasswordValid = true;

                // Jika checkbox Change Password dicentang
                if (toggleCheckbox.checked) {
                    const val = newPasswordInput.value;

                    // Regex Fix: Menggunakan \d agar lebih robust mendeteksi angka
                    const isLength = val.length >= 8;
                    const isCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
                    const isNum = /\d/.test(val);

                    updateRequirement(reqLength, isLength);
                    updateRequirement(reqCase, isCase);
                    updateRequirement(reqNum, isNum);

                    isPasswordValid = isLength && isCase && isNum;
                }

                if (isNameFilled && isEmailFilled && isPasswordValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400'); // Hapus warna disabled
                    submitBtn.classList.add('bg-black'); // Tambah warna aktif
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Listeners
            newPasswordInput.addEventListener('input', validateForm);
            fullName.addEventListener('input', validateForm);
            email.addEventListener('input', validateForm);
        });
    </script>
@endsection
