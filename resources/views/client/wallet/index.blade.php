@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-1">My Wallet</h1>
            <p class="text-gray-500 text-sm">Overview saldo, riwayat transaksi, dan level membership Anda.</p>
        </div>
    </div>

    {{-- LOGIC: Hitung Progress Tier & Stats --}}
    @php
        $totalPurchased = $wallet->total_purchased ?? 0;
        $currentBalance = $wallet->balance ?? 0;
        // Asumsi: Token terpakai = Total Beli - Saldo Saat Ini
        // (Bisa disesuaikan jika ada logika refund yang kompleks, tapi ini estimasi kasar yang cukup akurat)
        $totalUsed = max(0, $totalPurchased - $currentBalance);

        $currentTierName = $wallet->tier->name ?? 'Starter';

        // Threshold Logic
        $nextTierName = '';
        $targetAmount = 0;
        $progressPercent = 0;

        if ($totalPurchased < 50) {
            $nextTierName = 'Professional';
            $targetAmount = 50;
        } elseif ($totalPurchased < 200) {
            $nextTierName = 'Ultimate';
            $targetAmount = 200;
        } else {
            $nextTierName = 'Max Level';
            $targetAmount = $totalPurchased;
            $progressPercent = 100;
        }

        if ($nextTierName !== 'Max Level') {
            $remaining = $targetAmount - $totalPurchased;
            $progressPercent = ($totalPurchased / $targetAmount) * 100;
        } else {
            $remaining = 0;
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">

        {{-- CARD 1: AVAILABLE BALANCE (Main) --}}
        <div
            class="bg-[#111] text-white p-6 rounded-3xl shadow-xl shadow-black/5 relative overflow-hidden group flex flex-col justify-between">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity">
            </div>

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Available Balance</p>
                </div>
                <div class="flex items-baseline gap-1">
                    <h2 class="text-4xl font-bold tracking-tight">{{ number_format($currentBalance) }}</h2>
                    <span class="text-lg font-bold text-yellow-400">TX</span>
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

    {{-- TIER PROGRESS SECTION (Wide) --}}
    <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm mb-8 fade-in relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-full blur-3xl opacity-50 pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col md:flex-row gap-8 items-center">
            {{-- Left: Current Badge --}}
            <div class="flex-shrink-0 text-center md:text-left">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Current Tier</span>
                <div class="inline-flex items-center gap-3 px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl">
                    <div class="w-8 h-8 flex items-center justify-center">
                        @if (stripos($currentTierName, 'Ultimate') !== false)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-crown">
                                <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14" />
                            </svg>
                        @elseif(stripos($currentTierName, 'Professional') !== false)
                            <i data-feather="star" class="w-6 h-6 text-indigo-600"></i>
                        @else
                            <i data-feather="shield" class="w-6 h-6 text-gray-600"></i>
                        @endif
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ $currentTierName }}</span>
                </div>
            </div>

            {{-- Right: Progress Bar --}}
            <div class="flex-1 w-full">
                @if ($nextTierName !== 'Max Level')
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Unlock {{ $nextTierName }} Plan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Top up <strong
                                    class="text-black">{{ number_format($remaining) }} TX</strong> more to upgrade.</p>
                        </div>
                        <span class="text-2xl font-bold text-gray-200">{{ round($progressPercent) }}%</span>
                    </div>

                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-black h-full rounded-full transition-all duration-1000 relative"
                            style="width: {{ $progressPercent }}%">
                            <div class="absolute inset-0 bg-white/20 w-full h-full animate-pulse"></div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-full text-yellow-700">
                            <i data-feather="award" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Legendary Status!</h4>
                            <p class="text-sm text-gray-500">Anda telah mencapai tier tertinggi. Nikmati seluruh benefit
                                eksklusif.</p>
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
