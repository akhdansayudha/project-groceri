@extends('layouts.app')

@section('content')
    {{-- HEADER HERO SECTION --}}
    <section class="pt-40 pb-20 px-6 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto text-center relative z-10">
            <div class="reveal mb-6 inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-gray-50">
                <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Transparent Pricing</span>
            </div>

            <h1 class="reveal text-5xl md:text-7xl font-bold tracking-tighter mb-8 text-black leading-tight">
                Simple currency. <br> Powerful results.
            </h1>

            <p class="reveal text-lg md:text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed font-medium">
                We use <strong>Toratix (TX)</strong> credit system. Top-up your tokens and exchange them for high-quality
                creative services. No hidden fees.
            </p>
        </div>

        {{-- Background Decoration --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl pointer-events-none opacity-20">
            <div class="absolute top-20 left-10 w-64 h-64 bg-gray-100 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-50 rounded-full blur-3xl"></div>
        </div>
    </section>

    {{-- SECTION 1: TOKEN RATES (TOP UP PACKAGES) --}}
    <section class="py-24 px-6 bg-[#F3F4F6]">
        <div class="max-w-7xl mx-auto">
            <div class="reveal mb-16 flex flex-col md:flex-row justify-between items-end gap-6">
                <div>
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 tracking-tight">Toratix Rates</h2>
                    <p class="text-gray-500 text-lg">Purchase tokens according to your project needs.</p>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($tokenPackages as $pkg)
                    <div
                        class="reveal group bg-white p-8 rounded-[2.5rem] border border-gray-100 hover:border-black transition-all duration-500 hover:shadow-xl hover:-translate-y-2 relative overflow-hidden flex flex-col justify-between h-full">

                        {{-- Label Popular (Logic sederhana: paket ke-2 dianggap populer) --}}
                        @if ($loop->iteration == 2)
                            <div
                                class="absolute top-0 right-0 bg-black text-white text-[10px] font-bold px-5 py-2 rounded-bl-2xl uppercase tracking-wider">
                                Popular
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">
                                {{ $pkg->label ?? 'Package ' . $loop->iteration }}
                            </h3>
                            <div class="flex items-baseline gap-2 mb-6">
                                <span class="text-5xl font-bold text-black tracking-tighter">{{ $pkg->min_qty }}</span>
                                <span class="text-lg font-bold text-blue-600">TX</span>
                            </div>

                            @if ($pkg->min_qty != $pkg->max_qty)
                                <p class="text-xs text-gray-400 mb-6">Up to {{ $pkg->max_qty }} TX per transaction</p>
                            @endif
                        </div>

                        <div>
                            <div class="border-t border-gray-100 pt-6 mb-8">
                                <p class="text-xs font-bold uppercase text-gray-400 mb-2">Price per Token</p>
                                <p class="text-2xl font-bold text-black">Rp
                                    {{ number_format($pkg->price_per_token, 0, ',', '.') }}</p>
                            </div>

                            <a href="{{ route('login') }}"
                                class="block w-full py-4 rounded-xl border border-gray-200 text-center font-bold text-sm hover:bg-black hover:text-white transition-all duration-300 group-hover:border-black hover-target">
                                Purchase Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SECTION 2: SERVICE CATALOG --}}
    <section class="py-32 px-6 bg-white rounded-t-[4rem] -mt-10 relative z-20">
        <div class="max-w-7xl mx-auto">
            <div class="reveal text-center max-w-3xl mx-auto mb-24">
                <div class="mb-6 flex justify-center items-center gap-3">
                    <span class="w-2 h-2 bg-black rounded-full"></span>
                    <h3 class="text-sm font-bold uppercase tracking-widest">Service Catalog</h3>
                </div>
                <h2 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight">Exchange tokens for professional services.
                </h2>
                <p class="text-gray-500 text-lg">
                    Transparent pricing, maximum results. Choose the service that fits your current goal.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($services as $service)
                    <div
                        class="reveal group flex flex-col h-full bg-white border border-gray-100 p-8 md:p-10 rounded-[2.5rem] hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 hover:-translate-y-2">

                        {{-- Icon Header --}}
                        <div class="flex justify-between items-start mb-10">
                            <div
                                class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:bg-black group-hover:border-black transition-colors duration-500">
                                @if ($service->icon_url)
                                    {{-- Cek apakah icon URL adalah path storage atau nama feather icon --}}
                                    @if (Str::contains($service->icon_url, ['http', 'storage', 'services/']))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($service->icon_url) }}"
                                            class="w-8 h-8 object-contain group-hover:invert transition-all">
                                    @else
                                        <i data-feather="{{ $service->icon_url }}"
                                            class="w-7 h-7 text-gray-600 group-hover:text-white transition-colors"></i>
                                    @endif
                                @else
                                    <i data-feather="layers"
                                        class="w-7 h-7 text-gray-600 group-hover:text-white transition-colors"></i>
                                @endif
                            </div>

                            {{-- Price Tag --}}
                            <div
                                class="px-5 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-bold border border-blue-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-colors duration-500">
                                {{ $service->toratix_cost }} TX
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="mb-8 flex-1">
                            <h3 class="text-2xl font-bold mb-4 text-black group-hover:text-blue-600 transition-colors">
                                {{ $service->name }}</h3>
                            <p class="text-gray-500 leading-relaxed text-sm font-medium">
                                {{ $service->description }}
                            </p>
                        </div>

                        {{-- Action --}}
                        <div class="mt-auto pt-8 border-t border-gray-50 group-hover:border-gray-100 transition-colors">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 text-sm font-bold text-black group-hover:gap-4 transition-all duration-300 hover-target">
                                Order Service <i data-feather="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-32 px-6 bg-black text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="reveal text-5xl md:text-7xl font-bold mb-8 tracking-tighter">Ready to start?</h2>
            <p class="reveal text-gray-400 text-lg mb-12 max-w-xl mx-auto font-medium">
                Join our clients who have transformed their businesses through strategic design.
            </p>
            <div class="reveal flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                    class="px-10 py-5 bg-white text-black rounded-full font-bold hover:scale-105 transition-transform hover-target">
                    Start Project
                </a>
            </div>
        </div>
    </section>
@endsection
