@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <h1 class="text-3xl font-bold tracking-tight mb-1">Account Settings</h1>
        <p class="text-gray-500 text-sm">Kelola identitas, preferensi membership, dan keamanan akun Anda.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 flex items-center gap-2 fade-in">
            <i data-feather="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 fade-in">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.settings.update') }}" method="POST" enctype="multipart/form-data" class="fade-in">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: Profile & Tier Card --}}
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm text-center relative overflow-hidden">

                    {{-- Avatar Section --}}
                    <div class="relative w-32 h-32 mx-auto mb-4 group">
                        @php
                            $avatar = $user->avatar_url
                                ? (str_starts_with($user->avatar_url, 'http')
                                    ? $user->avatar_url
                                    : \Illuminate\Support\Facades\Storage::disk('supabase')->url($user->avatar_url))
                                : 'https://ui-avatars.com/api/?name=' .
                                    urlencode($user->full_name) .
                                    '&background=000&color=fff';
                        @endphp

                        <img id="avatarPreview" src="{{ $avatar }}"
                            class="w-full h-full rounded-full object-cover border-4 border-gray-50 shadow-inner group-hover:brightness-90 transition-all">

                        <label for="avatarInput"
                            class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-sm">
                            <i data-feather="camera" class="w-8 h-8"></i>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" class="hidden" onchange="previewImage(this)">
                    </div>

                    <h3 class="font-bold text-lg text-gray-900">{{ $user->full_name ?? 'User' }}</h3>
                    <p class="text-gray-500 text-xs">{{ $user->email }}</p>

                    {{-- PREMIUM TIER CARD DESIGN --}}
                    @php
                        $tierName = $user->wallet->tier->name ?? 'Starter';
                        $tierColor = match (true) {
                            stripos($tierName, 'Ultimate') !== false
                                => 'from-yellow-700 to-yellow-900 border-yellow-600 text-yellow-100',
                            stripos($tierName, 'Professional') !== false
                                => 'from-blue-700 to-blue-900 border-blue-600 text-blue-100',
                            default => 'from-gray-700 to-gray-900 border-gray-600 text-gray-200',
                        };
                    @endphp

                    <div
                        class="mt-6 mx-2 p-4 rounded-2xl bg-gradient-to-br {{ $tierColor }} shadow-lg relative overflow-hidden text-left border border-white/10 group">
                        <div
                            class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all">
                        </div>

                        <div class="flex items-center justify-between mb-2 relative z-10">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Current Membership</p>
                            @if (stripos($tierName, 'Ultimate') !== false)
                                <i data-feather="award" class="w-4 h-4 text-yellow-400"></i>
                            @elseif(stripos($tierName, 'Professional') !== false)
                                <i data-feather="star" class="w-4 h-4 text-blue-300"></i>
                            @else
                                <i data-feather="shield" class="w-4 h-4 text-gray-400"></i>
                            @endif
                        </div>
                        <h4 class="text-2xl font-bold tracking-tight relative z-10">{{ $tierName }}</h4>
                        <div class="mt-3 pt-3 border-t border-white/10 flex justify-between items-center relative z-10">
                            <span class="text-[10px] opacity-80">Benefits Active</span>
                            <a href="{{ route('client.wallet.index') }}"
                                class="text-[10px] font-bold underline hover:text-white transition-colors">Upgrade</a>
                        </div>
                    </div>

                    {{-- LAST UPDATED INFO --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 font-medium flex items-center justify-center gap-1.5">
                            <i data-feather="clock" class="w-3 h-3"></i>
                            Last Profile Update:
                            <span
                                class="text-gray-600">{{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Form Inputs --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- SECTION 1: PERSONAL INFO --}}
                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                        <i data-feather="user" class="w-5 h-5"></i> Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Full Name</label>
                            {{-- PERBAIKAN: Menggunakan full_name --}}
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black transition-colors font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-black transition-colors font-medium">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: SECURITY --}}
                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                        <i data-feather="lock" class="w-5 h-5"></i> Security & Password
                    </h3>

                    <div class="space-y-5">
                        {{-- Current Password --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" placeholder="••••••••"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-black transition-colors font-medium">
                                <button type="button" onclick="togglePassword('current_password')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Kosongkan jika tidak ingin mengubah password.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- New Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">New Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="new_password" placeholder="••••••••"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-black transition-colors font-medium">
                                    <button type="button" onclick="togglePassword('new_password')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- Confirm Password --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password_confirmation"
                                        id="new_password_confirmation" placeholder="••••••••"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:border-black transition-colors font-medium">
                                    <button type="button" onclick="togglePassword('new_password_confirmation')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex justify-end gap-3 pt-4">
                    <button type="reset"
                        class="px-6 py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-8 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg shadow-black/20 flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i>
                        Save Changes
                    </button>
                </div>

            </div>
        </div>
    </form>

    {{-- SCRIPTS --}}
    <script>
        // Preview Image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle Password Visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('svg'); // Select feather icon svg

            if (input.type === "password") {
                input.type = "text";
                // Secara visual feather icon tidak berubah otomatis, tapi fungsinya jalan.
                // Jika ingin ubah icon, perlu re-render feather atau toggle class manual.
                // Disini kita ubah style buttonnya saja sebagai indikator
                input.nextElementSibling.classList.add('text-black');
                input.nextElementSibling.classList.remove('text-gray-400');
            } else {
                input.type = "password";
                input.nextElementSibling.classList.remove('text-black');
                input.nextElementSibling.classList.add('text-gray-400');
            }
        }
    </script>
@endsection
