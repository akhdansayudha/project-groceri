@extends('staff.layouts.app')

@section('content')
    <div class="fade-in pb-20">

        {{-- HEADER --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Account Settings</h1>
            <p class="text-gray-500 mt-1">Manage your profile information, payment details, and security.</p>
        </div>

        {{-- GLOBAL NOTIFICATIONS --}}
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
                    <span class="font-bold text-sm">Terdapat kesalahan pada input Anda:</span>
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

            {{-- COLUMN LEFT: PROFILE & SECURITY --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- 1. PUBLIC PROFILE CARD --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="user" class="w-4 h-4 text-gray-400"></i> Public Profile
                        </h3>
                        <span class="text-[10px] text-gray-400 font-medium">
                            Last updated: {{ $user->updated_at->diffForHumans() }}
                        </span>
                    </div>

                    <form action="{{ route('staff.settings.profile') }}" method="POST" enctype="multipart/form-data"
                        class="p-8">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col md:flex-row gap-8 items-start">

                            {{-- Avatar Upload --}}
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
                                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all font-medium text-gray-900">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email
                                        Address</label>
                                    <div class="relative">
                                        <input type="email" value="{{ $user->email }}" disabled
                                            class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed">
                                        <i data-feather="lock"
                                            class="w-4 h-4 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2"></i>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Email cannot be changed directly.</p>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit"
                                        class="px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 2. SECURITY CARD (With Requirements & Stable Eye Toggle) --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden" x-data="{
                    newPassword: '',
                    showCurrent: false,
                    showNew: false,
                    showConfirm: false,
                    get requirements() {
                        return [
                            { label: 'Minimum 8 characters', met: this.newPassword.length >= 8 },
                            { label: 'Mixed Case (Upper & Lower)', met: /[a-z]/.test(this.newPassword) && /[A-Z]/.test(this.newPassword) },
                            { label: 'Contains Number (0-9)', met: /\d/.test(this.newPassword) }
                        ]
                    }
                }">

                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="lock" class="w-4 h-4 text-gray-400"></i> Password & Security
                        </h3>
                    </div>

                    <form action="{{ route('staff.settings.password') }}" method="POST" class="p-8 space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Current Password --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Current Password</label>
                            <div class="relative">
                                <input :type="showCurrent ? 'text' : 'password'" name="current_password"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                <button type="button" @click="showCurrent = !showCurrent"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black focus:outline-none">
                                    {{-- Inline SVG untuk kestabilan --}}
                                    <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg x-show="showCurrent" style="display: none;" xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                        </path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 relative">
                            {{-- New Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">New Password</label>
                                <div class="relative">
                                    <input :type="showNew ? 'text' : 'password'" name="new_password" x-model="newPassword"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                    <button type="button" @click="showNew = !showNew"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black focus:outline-none">
                                        <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg x-show="showNew" style="display: none;" xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                            </path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Confirm
                                    Password</label>
                                <div class="relative">
                                    <input :type="showConfirm ? 'text' : 'password'" name="new_password_confirmation"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all pr-12">
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black focus:outline-none">
                                        <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg x-show="showConfirm" style="display: none;"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                            </path>
                                            <line x1="1" y1="1" x2="23" y2="23"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Password Requirements Checklist --}}
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100" x-show="newPassword.length > 0"
                            x-transition>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Password
                                Requirements</p>
                            <ul class="space-y-1.5">
                                <template x-for="req in requirements">
                                    <li class="flex items-center gap-2 text-xs"
                                        :class="req.met ? 'text-green-600 font-bold' : 'text-gray-500'">
                                        <div class="w-4 h-4 rounded-full flex items-center justify-center border"
                                            :class="req.met ? 'bg-green-100 border-green-200' : 'bg-gray-200 border-gray-300'">
                                            <svg x-show="req.met" xmlns="http://www.w3.org/2000/svg" width="10"
                                                height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <span x-text="req.label"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- COLUMN RIGHT: BANKING INFO --}}
            <div class="space-y-8" x-data="{ editing: {{ $user->bank_account ? 'false' : 'true' }} }">

                {{-- 3. PAYMENT DETAILS CARD --}}
                <div
                    class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-3xl shadow-xl overflow-hidden text-white relative">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl -mr-10 -mt-10">
                    </div>

                    <div class="px-8 py-6 border-b border-white/10 flex justify-between items-center relative z-10">
                        <h3 class="font-bold flex items-center gap-2">
                            <i data-feather="credit-card" class="w-4 h-4 text-blue-200"></i> Payment Details
                        </h3>

                        {{-- Edit Badge (Visual Only) --}}
                        <span x-show="editing"
                            class="text-[10px] bg-blue-500 text-white px-2 py-1 rounded font-bold animate-pulse">
                            EDIT MODE
                        </span>
                    </div>

                    {{-- Form Starts Here --}}
                    <form action="{{ route('staff.settings.bank') }}" method="POST" class="p-8 relative z-10 space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-[10px] font-bold text-blue-200 uppercase mb-2">Bank Name</label>
                            <div class="relative">
                                <select name="bank_name" :disabled="!editing"
                                    class="w-full px-4 py-3 bg-blue-900/50 border border-blue-500/30 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all placeholder-blue-300 disabled:opacity-70 disabled:cursor-not-allowed appearance-none">
                                    <option value="" disabled selected>Select Bank</option>
                                    @php $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'BSI', 'Jago', 'Jenius', 'Permata']; @endphp
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank }}"
                                            {{ old('bank_name', $user->bank_name) == $bank ? 'selected' : '' }}
                                            class="text-black">
                                            {{ $bank }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Custom Chevron for Select --}}
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-blue-200 uppercase mb-2">Account Number</label>
                            <input type="number" name="bank_account"
                                value="{{ old('bank_account', $user->bank_account) }}" :disabled="!editing"
                                class="w-full px-4 py-3 bg-blue-900/50 border border-blue-500/30 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all placeholder-blue-300/50 disabled:opacity-70 disabled:cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-blue-200 uppercase mb-2">Account Holder
                                Name</label>
                            <input type="text" name="bank_holder"
                                value="{{ old('bank_holder', $user->bank_holder) }}" :disabled="!editing"
                                class="w-full px-4 py-3 bg-blue-900/50 border border-blue-500/30 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all placeholder-blue-300/50 disabled:opacity-70 disabled:cursor-not-allowed">
                        </div>

                        {{-- Action Buttons Area (Fixed Position) --}}
                        <div class="pt-4 h-12 relative">
                            {{-- Button: Edit (Tampil saat tidak editing) --}}
                            <button type="button" x-show="!editing" @click="editing = true"
                                class="absolute inset-0 w-full py-3 bg-white/10 text-white rounded-xl font-bold text-sm hover:bg-white/20 transition-all border border-white/10 flex items-center justify-center gap-2">
                                <i data-feather="edit-2" class="w-4 h-4"></i> Edit Payment Details
                            </button>

                            {{-- Button: Save & Cancel (Tampil saat editing) --}}
                            <div x-show="editing" style="display: none;" class="absolute inset-0 w-full flex gap-3">
                                <button type="button" @click="editing = false"
                                    class="w-1/3 py-3 bg-white/10 text-white rounded-xl font-bold text-sm hover:bg-white/20 transition-all border border-white/10">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="w-2/3 py-3 bg-white text-blue-900 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all shadow-lg">
                                    Save Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ALERT INFO --}}
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

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
