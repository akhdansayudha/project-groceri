@extends('staff.layouts.app')

@section('content')
    <div class="fade-in pb-20">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Account Settings</h1>
            <p class="text-gray-500 mt-1">Manage your profile information, payment details, and security.</p>
        </div>

        {{-- NOTIFICATIONS --}}
        @if (session('success'))
            <div
                class="mb-8 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
                <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <i data-feather="alert-circle" class="w-5 h-5 text-red-600"></i>
                    <span class="font-bold text-sm">Action Failed:</span>
                </div>
                <ul class="list-disc list-inside text-xs ml-8 text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN: PROFILE & SECURITY --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- 1. PUBLIC PROFILE --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="user" class="w-4 h-4 text-gray-400"></i> Public Profile
                        </h3>
                        <span class="text-[10px] text-gray-400 font-medium">Last updated:
                            {{ $user->updated_at->diffForHumans() }}</span>
                    </div>

                    <form action="{{ route('staff.settings.profile') }}" method="POST" enctype="multipart/form-data"
                        class="p-8">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col md:flex-row gap-8 items-start">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 relative group">
                                <div
                                    class="w-24 h-24 rounded-full bg-gray-100 overflow-hidden border border-gray-200 shadow-inner">
                                    <img id="avatar-preview"
                                        src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <label for="avatar-input"
                                    class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white text-xs font-bold">
                                    Change
                                </label>
                                <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>

                            {{-- Inputs --}}
                            <div class="flex-1 w-full space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Full Name</label>
                                    <input type="text" name="full_name" id="fullNameInput"
                                        value="{{ old('full_name', $user->full_name) }}"
                                        data-original="{{ $user->full_name }}"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all font-medium text-gray-900">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email
                                        Address</label>
                                    <input type="email" value="{{ $user->email }}" disabled
                                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed">
                                    <p class="text-[10px] text-gray-400 mt-1">Email cannot be changed directly.</p>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" id="saveProfileBtn" disabled
                                        class="px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 2. PASSWORD & SECURITY --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="lock" class="w-4 h-4 text-gray-400"></i> Password & Security
                        </h3>
                    </div>

                    <form action="{{ route('staff.settings.password') }}" method="POST" class="p-8 space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Current Pass --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="currentPass" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                <button type="button"
                                    class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black outline-none"
                                    data-target="currentPass">
                                    <i data-feather="eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- New Pass & Confirm --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">New Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="newPass" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                    <button type="button"
                                        class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black outline-none"
                                        data-target="newPass">
                                        <i data-feather="eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Confirm
                                    Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password_confirmation" id="confirmPass" required
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                    <button type="button"
                                        class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black outline-none"
                                        data-target="confirmPass">
                                        <i data-feather="eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Requirements & Match Check --}}
                        <div id="password-feedback"
                            class="hidden bg-gray-50 p-4 rounded-xl border border-gray-100 transition-all">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-2">Password Requirements</p>
                            <ul class="space-y-1 text-xs text-gray-500">
                                <li id="req-length" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Minimum 8 characters
                                </li>
                                <li id="req-case" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Mixed Case (Upper & Lower) & Number
                                </li>
                                <li id="req-match" class="flex items-center gap-2">
                                    <i data-feather="circle" class="w-3 h-3"></i> Passwords Match
                                </li>
                            </ul>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" id="updatePassBtn" disabled
                                class="px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10 disabled:opacity-50 disabled:cursor-not-allowed">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: PAYMENT DETAILS (BLACK CARD) --}}
            <div class="space-y-8">
                <div class="bg-black text-white rounded-3xl shadow-xl overflow-hidden relative">
                    {{-- Header --}}
                    <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center relative z-10">
                        <h3 class="font-bold flex items-center gap-2">
                            <i data-feather="credit-card" class="w-4 h-4 text-gray-400"></i> Payment Details
                        </h3>
                        <div id="editBadge"
                            class="hidden text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold animate-pulse">
                            EDIT MODE
                        </div>
                    </div>

                    {{-- Decoration --}}
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-gray-800 rounded-full opacity-30 blur-2xl -mr-10 -mt-10 pointer-events-none">
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('staff.settings.bank') }}" method="POST" id="paymentForm"
                        class="p-8 relative z-10 space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Hidden inputs to store original values for cancel --}}
                        <input type="hidden" id="orig_bank_name" value="{{ $user->bank_name }}">
                        <input type="hidden" id="orig_bank_account" value="{{ $user->bank_account }}">
                        <input type="hidden" id="orig_bank_holder" value="{{ $user->bank_holder }}">

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Bank Name</label>
                            <div class="relative">
                                <select name="bank_name" id="bank_name" disabled
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-white focus:border-transparent transition-all appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="" disabled selected>Select Bank</option>
                                    @foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'BSI', 'Jago', 'Jenius', 'Permata'] as $bank)
                                        <option value="{{ $bank }}"
                                            {{ old('bank_name', $user->bank_name) == $bank ? 'selected' : '' }}
                                            class="text-black">{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Account Number</label>
                            <input type="number" name="bank_account" id="bank_account"
                                value="{{ old('bank_account', $user->bank_account) }}" disabled
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-white focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Account Holder
                                Name</label>
                            <input type="text" name="bank_holder" id="bank_holder"
                                value="{{ old('bank_holder', $user->bank_holder) }}" disabled
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-white focus:ring-2 focus:ring-white focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        </div>

                        {{-- Actions --}}
                        <div class="pt-4">
                            <button type="button" id="editPaymentBtn"
                                class="w-full py-3 bg-white text-black rounded-xl font-bold text-sm hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                                <i data-feather="edit-2" class="w-4 h-4"></i> Edit Payment Details
                            </button>

                            <div id="savePaymentContainer" class="hidden flex gap-3">
                                <button type="button" id="cancelPaymentBtn"
                                    class="w-1/3 py-3 bg-gray-800 text-white rounded-xl font-bold text-sm hover:bg-gray-700 transition-all border border-gray-700">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-2/3 py-3 bg-white text-black rounded-xl font-bold text-sm hover:bg-gray-200 transition-all shadow-lg">
                                    Save Payment Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- INFO --}}
                <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-5 flex gap-3">
                    <i data-feather="info" class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-yellow-800">Important Note</h4>
                        <p class="text-xs text-yellow-700 mt-1 leading-relaxed">
                            Ensure your bank details match your ID. Mismatched details may cause payout rejection.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. PROFILE LOGIC ---
            const avatarInput = document.getElementById('avatar-input');
            const fullNameInput = document.getElementById('fullNameInput');
            const saveProfileBtn = document.getElementById('saveProfileBtn');
            const originalName = fullNameInput.getAttribute('data-original');

            function checkProfileChanges() {
                const nameChanged = fullNameInput.value.trim() !== originalName;
                const fileSelected = avatarInput.files.length > 0;
                saveProfileBtn.disabled = !(nameChanged || fileSelected);
            }

            fullNameInput.addEventListener('input', checkProfileChanges);
            avatarInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        document.getElementById('avatar-preview').src = evt.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
                checkProfileChanges();
            });

            // --- 2. PASSWORD LOGIC ---
            const currentPass = document.getElementById('currentPass');
            const newPass = document.getElementById('newPass');
            const confirmPass = document.getElementById('confirmPass');
            const updatePassBtn = document.getElementById('updatePassBtn');
            const feedbackBox = document.getElementById('password-feedback');

            // Toggles
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i'); // Ambil elemen i (feather icon)

                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    // Ganti atribut data-feather lalu panggil feather.replace()
                    if (type === 'text') {
                        icon.setAttribute('data-feather', 'eye-off');
                    } else {
                        icon.setAttribute('data-feather', 'eye');
                    }
                    feather.replace(); // Render ulang ikon
                });
            });

            // Validation
            function validatePassword() {
                const val = newPass.value;
                const confirmVal = confirmPass.value;
                const currentVal = currentPass.value;

                if (val.length > 0) feedbackBox.classList.remove('hidden');
                else feedbackBox.classList.add('hidden');

                // Regex checks
                const isLength = val.length >= 8;
                const isComplex = /[a-z]/.test(val) && /[A-Z]/.test(val) && /\d/.test(val);
                const isMatch = val === confirmVal && val !== '';

                updateReqUI('req-length', isLength);
                updateReqUI('req-case', isComplex);
                updateReqUI('req-match', isMatch);

                // Enable button only if all valid AND current pass filled
                updatePassBtn.disabled = !(isLength && isComplex && isMatch && currentVal !== '');
            }

            function updateReqUI(id, valid) {
                const el = document.getElementById(id);
                if (valid) {
                    el.classList.remove('text-gray-500');
                    el.classList.add('text-green-600', 'font-bold');
                    el.querySelector('svg').outerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check text-green-600"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                } else {
                    el.classList.remove('text-green-600', 'font-bold');
                    el.classList.add('text-gray-500');
                    el.querySelector('svg').outerHTML =
                        `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle text-gray-400"><circle cx="12" cy="12" r="10"></circle></svg>`;
                }
            }

            newPass.addEventListener('input', validatePassword);
            confirmPass.addEventListener('input', validatePassword);
            currentPass.addEventListener('input', validatePassword);

            // --- 3. PAYMENT DETAILS LOGIC ---
            const editBtn = document.getElementById('editPaymentBtn');
            const cancelBtn = document.getElementById('cancelPaymentBtn');
            const saveContainer = document.getElementById('savePaymentContainer');
            const editBadge = document.getElementById('editBadge');
            const inputs = ['bank_name', 'bank_account', 'bank_holder'];

            editBtn.addEventListener('click', function() {
                togglePaymentForm(true);
            });

            cancelBtn.addEventListener('click', function() {
                togglePaymentForm(false);
                // Reset values
                inputs.forEach(id => {
                    document.getElementById(id).value = document.getElementById('orig_' + id).value;
                });
            });

            function togglePaymentForm(editable) {
                inputs.forEach(id => {
                    const el = document.getElementById(id);
                    el.disabled = !editable;

                    if (editable) {
                        // MODE EDIT: Background Terang, Teks Hitam
                        el.classList.remove('bg-gray-900', 'border-gray-700', 'text-white');
                        el.classList.add('bg-gray-50', 'border-gray-200', 'text-black');
                    } else {
                        // MODE LOCK: Background Gelap, Teks Putih
                        el.classList.add('bg-gray-900', 'border-gray-700', 'text-white');
                        el.classList.remove('bg-gray-50', 'border-gray-200', 'text-black');
                    }
                });

                if (editable) {
                    editBtn.classList.add('hidden');
                    saveContainer.classList.remove('hidden');
                    editBadge.classList.remove('hidden');
                } else {
                    editBtn.classList.remove('hidden');
                    saveContainer.classList.add('hidden');
                    editBadge.classList.add('hidden');
                }
            }
        });
    </script>
@endsection
