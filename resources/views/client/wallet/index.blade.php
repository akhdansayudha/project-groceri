@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-1">My Wallet</h1>
            <p class="text-gray-500 text-sm">Overview saldo, riwayat transaksi, dan level membership Anda.</p>
        </div>
    </div>

    {{-- LOGIC: Hitung Progress Per-Tier (FIXED) --}}
    @php
        $totalPurchased = $wallet->total_purchased ?? 0;
        $currentBalance = $wallet->balance ?? 0;
        $totalUsed = max(0, $totalPurchased - $currentBalance);

        // 1. Urutkan Tier & Ambil Current Tier
        $sortedTiers = $tiers->sortBy('min_toratix')->values();
        $currentTier = $wallet->tier;

        // Cari posisi index tier saat ini di array sorted
        $currentIdx = $sortedTiers->search(function ($t) use ($currentTier) {
            return $t->id === $currentTier->id;
        });

        // 2. Tentukan Next Tier & Max Level Status
        $nextTier = null;
        $isMaxLevel = false;

        if ($currentIdx !== false && isset($sortedTiers[$currentIdx + 1])) {
            $nextTier = $sortedTiers[$currentIdx + 1];
        } else {
            $isMaxLevel = true;
        }

        // 3. Kalkulasi Angka
        $progressPercent = 0;
        $remaining = 0;
        $barStartLabel = 0;
        $barEndLabel = 0;

        if (!$isMaxLevel && $nextTier) {
            // Target: Batas minimum untuk masuk tier berikutnya
            $targetToLevelUp = $nextTier->min_toratix;

            // Hitung sisa topup yang dibutuhkan
            $remaining = max(0, $targetToLevelUp - $totalPurchased);

            // Progress Bar Range: Dari Min ke Max pada tier SAAT INI
            // Contoh Professional: 50 s/d 199.
            $barStartLabel = $currentTier->min_toratix;
            $barEndLabel = $currentTier->max_toratix;

            // Hitung progress user di DALAM tier ini
            // Rumus: (TotalUser - MinTierIni) / (MaxTierIni - MinTierIni) * 100
            $userProgressInThisTier = $totalPurchased - $barStartLabel;
            $tierRangeTotal = $barEndLabel - $barStartLabel;

            if ($tierRangeTotal > 0) {
                $progressPercent = ($userProgressInThisTier / $tierRangeTotal) * 100;
            } else {
                $progressPercent = 100;
            }
        } else {
            // Sudah Max Tier
            $progressPercent = 100;
        }

        // Pastikan tidak minus atau lebih dari 100 visualnya
        $progressPercent = min(100, max(0, $progressPercent));
    @endphp

    {{-- GRID STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">

        {{-- CARD 1: AVAILABLE BALANCE (Main) --}}
        <div
            class="bg-[#0f0f0f] text-white p-6 rounded-3xl shadow-2xl shadow-black/20 relative overflow-hidden group flex flex-col justify-between border border-gray-800">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-gray-800 to-transparent rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity">
            </div>

            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Available Balance</p>
                </div>
                <div class="flex items-baseline gap-1">
                    <h2 class="text-4xl font-bold tracking-tight">{{ number_format($currentBalance) }}</h2>
                    <span class="text-lg font-bold text-yellow-500">TX</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('client.wallet.topup') }}"
                    class="w-full py-3 bg-white text-black rounded-xl font-bold text-xs hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    <i data-feather="plus-circle" class="w-4 h-4"></i>
                    Top Up Now
                </a>
            </div>
        </div>

        {{-- CARD 2: LIFETIME PURCHASED --}}
        <div
            class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-green-200 transition-colors group">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="arrow-down-left" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold bg-green-50 text-green-700 px-2 py-1 rounded-full uppercase">Total
                    In</span>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Lifetime Purchased</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalPurchased) }} <span
                        class="text-xs text-gray-400">TX</span></h3>
                <p class="text-[10px] text-gray-400 mt-2">Total token yang pernah Anda beli.</p>
            </div>
        </div>

        {{-- CARD 3: TOKENS USED --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-red-200 transition-colors group">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-feather="arrow-up-right" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-bold bg-red-50 text-red-600 px-2 py-1 rounded-full uppercase">Total Out</span>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Tokens Used</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalUsed) }} <span
                        class="text-xs text-gray-400">TX</span></h3>
                <p class="text-[10px] text-gray-400 mt-2">Total token yang digunakan untuk project.</p>
            </div>
        </div>
    </div>

    {{-- TIER PROGRESS SECTION (EXCLUSIVE DARK & GOLD DESIGN) --}}
    <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm mb-8 fade-in relative overflow-hidden">
        {{-- Background Effects --}}
        <div
            class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-b from-gray-50 via-gray-50 to-transparent rounded-full blur-3xl opacity-60 pointer-events-none -mr-20 -mt-20">
        </div>

        <div class="relative z-10 flex flex-col md:flex-row gap-10 items-center">

            {{-- Left: EXCLUSIVE BADGE DESIGN --}}
            <div class="flex-shrink-0 text-center md:text-left">
                <span
                    class="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-4 block text-center md:text-left">Current
                    Membership</span>

                @php
                    $tierName = $currentTier->name ?? 'Starter';
                    // Cek jika Ultimate/Max level untuk efek khusus
                    $isUltimate = stripos($tierName, 'Ultimate') !== false || $isMaxLevel;
                @endphp

                <div class="relative group cursor-default inline-block">
                    {{-- Glow Effect belakang badge (Gold untuk semua agar exclusive) --}}
                    <div
                        class="absolute -inset-0.5 bg-gradient-to-r from-[#C6A355] to-[#F2E49B] rounded-2xl blur opacity-30 group-hover:opacity-50 transition duration-1000">
                    </div>

                    {{-- Badge Container --}}
                    <div
                        class="relative w-[240px] h-[150px] bg-[#0F0F0F] rounded-2xl border border-yellow-900/30 flex flex-col items-center justify-center shadow-2xl overflow-hidden">

                        {{-- Pattern Overlay --}}
                        <div class="absolute inset-0 opacity-10"
                            style="background-image: radial-gradient(#C6A355 1px, transparent 1px); background-size: 12px 12px;">
                        </div>

                        {{-- Icon --}}
                        <div class="mb-3 relative z-10">
                            @if ($isUltimate)
                                <div
                                    class="p-3 rounded-full bg-gradient-to-b from-[#8E793E] to-[#AD974F] shadow-lg shadow-yellow-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14" />
                                    </svg>
                                </div>
                            @else
                                <div
                                    class="p-3 rounded-full bg-gradient-to-b from-gray-800 to-black border border-gray-700 shadow-lg">
                                    <i data-feather="shield" class="w-6 h-6 text-[#C6A355]"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Text Gradient Gold/Silver --}}
                        <span
                            class="text-xl font-black tracking-[0.2em] uppercase relative z-10 text-transparent bg-clip-text bg-gradient-to-r from-[#C6A355] via-[#F2E49B] to-[#C6A355] drop-shadow-sm">
                            {{ $tierName }}
                        </span>

                        {{-- Underline --}}
                        <div
                            class="h-[1px] w-12 bg-gradient-to-r from-transparent via-[#C6A355] to-transparent mt-3 opacity-70">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Progress Bar & Info --}}
            <div class="flex-1 w-full pl-0 md:pl-4">
                @if (!$isMaxLevel && $nextTier)
                    {{-- NOT MAX LEVEL VIEW --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="text-xl font-bold text-gray-900">Menuju {{ $nextTier->name }}</h4>
                            <span
                                class="bg-black text-white text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider">Next
                                Tier</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed max-w-xl">
                            Top up sebanyak <strong
                                class="text-black bg-yellow-100 px-1 rounded">{{ number_format($remaining) }} TX</strong>
                            lagi untuk mencapai tier {{ $nextTier->name }}.
                        </p>
                    </div>

                    {{-- Progress Bar Container --}}
                    <div class="relative pt-2">
                        <div class="flex justify-between text-xs font-bold text-gray-400 mb-2">
                            <span>{{ number_format($barStartLabel) }} TX ({{ $currentTier->name }})</span>
                            <span class="text-black">{{ number_format($barEndLabel) }} TX</span>
                        </div>

                        {{-- Bar Background --}}
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                            {{-- Bar Fill (Gradient Gold) --}}
                            <div class="h-full rounded-full transition-all duration-1000 relative bg-gradient-to-r from-[#8E793E] via-[#C6A355] to-[#F2E49B] shadow-[0_0_10px_rgba(198,163,85,0.5)]"
                                style="width: {{ $progressPercent }}%">
                                <div class="absolute inset-0 bg-white/20 w-full h-full animate-[pulse_3s_infinite]"></div>
                            </div>
                        </div>
                        <div class="text-right mt-1">
                            <span class="text-[10px] font-bold text-gray-400">{{ round($progressPercent) }}%
                                Completed</span>
                        </div>
                    </div>

                    {{-- Benefit Teaser --}}
                    <div
                        class="mt-6 flex items-center gap-3 text-xs text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 w-fit">
                        <div class="bg-white p-1.5 rounded-lg border border-gray-200 shadow-sm"><i data-feather="unlock"
                                class="w-3 h-3 text-black"></i></div>
                        <span>Unlock <strong>{{ $nextTier->max_active_tasks }} Active Tasks</strong> limit at
                            {{ $nextTier->name }} level.</span>
                    </div>
                @else
                    {{-- MAX LEVEL ACHIEVEMENT VIEW --}}
                    <div
                        class="bg-[#0a0a0a] rounded-2xl p-8 relative overflow-hidden border border-[#C6A355]/30 shadow-2xl">
                        {{-- Ambient Light --}}
                        <div class="absolute top-0 right-0 w-64 h-64 bg-[#C6A355] rounded-full blur-[120px] opacity-20">
                        </div>

                        <div class="relative z-10 flex flex-col md:flex-row items-start gap-6">
                            <div
                                class="p-4 bg-gradient-to-br from-[#8E793E] to-[#C6A355] rounded-2xl text-white shadow-lg shadow-[#C6A355]/20">
                                <i data-feather="award" class="w-10 h-10"></i>
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold text-white flex flex-wrap items-center gap-3 mb-2">
                                    {{ $currentTier->name }} Achieved!
                                    <span
                                        class="text-[10px] bg-[#F2E49B] text-black px-2 py-0.5 rounded font-black tracking-widest uppercase shadow-md">Highest
                                        Tier</span>
                                </h4>
                                <p class="text-sm text-gray-400 leading-relaxed max-w-lg">
                                    Luar biasa! Anda telah mencapai tier tertinggi di Vektora. Nikmati prioritas layanan
                                    tertinggi, batas project maksimal, dan akses eksklusif ke semua fitur kami.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TRANSACTION HISTORY TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i data-feather="clock" class="w-4 h-4 text-gray-600"></i>
                </div>
                <h3 class="font-bold text-lg">Transaction History</h3>
            </div>

            <div class="hidden md:block">
                <button class="text-xs font-bold text-gray-400 hover:text-black flex items-center gap-1">
                    View All <i data-feather="chevron-right" class="w-3 h-3"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-4">Description</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Type</th>
                        <th class="px-8 py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($transactions as $trx)
                        @php
                            $isPositive = in_array($trx->type, ['topup', 'credit', 'refund']);
                            $isRefund = $trx->type === 'refund';
                        @endphp

                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-4">
                                <span class="font-bold text-gray-900 block">{{ $trx->description }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">ID: {{ substr($trx->id, 0, 8) }}</span>
                            </td>

                            <td class="px-8 py-4 text-gray-500">
                                {{ $trx->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="px-8 py-4">
                                @if ($trx->type == 'topup' || $trx->type == 'credit')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 uppercase tracking-wide border border-green-100">
                                        <i data-feather="arrow-down-left" class="w-3 h-3"></i> Top Up
                                    </span>
                                @elseif ($trx->type == 'refund')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wide border border-blue-100">
                                        <i data-feather="refresh-ccw" class="w-3 h-3"></i> Refund
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wide border border-gray-200">
                                        <i data-feather="arrow-up-right" class="w-3 h-3"></i> Usage
                                    </span>
                                @endif
                            </td>

                            <td
                                class="px-8 py-4 text-right font-bold text-base {{ $isPositive ? ($isRefund ? 'text-blue-600' : 'text-green-600') : 'text-gray-900' }}">
                                {{ $isPositive ? '+' : '-' }} {{ number_format($trx->amount) }} TX
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                        <i data-feather="inbox" class="w-8 h-8"></i>
                                    </div>
                                    <p class="text-gray-900 font-bold text-lg">No transactions yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Transaction history will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="px-8 py-4 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
