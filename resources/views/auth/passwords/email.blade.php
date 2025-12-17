@extends('layouts.app')

@section('content')
    <div class="min-h-screen w-full flex items-center justify-center bg-[#F3F4F6] p-4 relative overflow-hidden">
        {{-- Background Elements --}}
        <div
            class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-blue-100 rounded-full blur-[150px] opacity-40 pointer-events-none">
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl w-full max-w-lg p-10 relative z-10 border border-gray-100">

            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold mb-2">Forgot Password?</h2>
                <p class="text-gray-500 text-sm">No worries, we'll send you reset instructions.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-100 flex items-center gap-3">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-500"></i>
                    <span class="text-sm font-bold text-green-700">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-1">Enter your email</label>
                    <div class="relative">
                        <input type="email" name="email" required placeholder="name@example.com"
                            class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 text-black outline-none focus:border-black transition-all font-medium">
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i data-feather="mail" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-black text-white font-bold text-lg py-4 rounded-2xl hover:scale-[1.02] transition-all shadow-xl shadow-black/10">
                    Send Reset Link
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('login') }}"
                    class="text-sm font-bold text-gray-400 hover:text-black transition-colors flex items-center justify-center gap-2">
                    <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
@endsection
