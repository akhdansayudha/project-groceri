@extends('staff.layouts.app')

@section('content')
    {{-- FIX: Wrapper x-data dipisah dari class visual (.fade-in) agar position:fixed modal bekerja --}}
    <div x-data="{
        payoutModalOpen: false,
        tokenInput: 0,
        rate: {{ $rate }},
        maxBalance: {{ $user->wallet->balance ?? 0 }},
        hasBank: {{ !empty($user->bank_account) && !empty($user->bank_name) ? 'true' : 'false' }}
    }">

        {{-- MAIN CONTENT CONTAINER --}}
        <div class="fade-in pb-20">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Earnings & Payouts</h1>
                <p class="text-gray-500 mt-1">Manage your wallet, track income, and withdraw your earnings.</p>
            </div>

            {{-- NOTIFICATIONS AREA --}}
            @if (session('success'))
                <div
                    class="mb-8 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm animate-fade-in-up">
                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">Success</h4>
                        <p class="text-xs">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-8 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 flex items-center gap-3 shadow-sm animate-fade-in-up">
                    <div class="p-2 bg-red-100 rounded-lg text-red-600">
                        <i data-feather="x-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">Action Failed</h4>
                        <p class="text-xs">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-8 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 shadow-sm animate-fade-in-up">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-red-100 rounded-lg text-red-600">
                            <i data-feather="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <span class="font-bold text-sm">Please check your input:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs ml-12 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- WARNING: BANK DETAILS MISSING --}}
            @if (empty($user->bank_account) || empty($user->bank_name))
                <div
                    class="mb-8 bg-orange-50 border border-orange-200 rounded-2xl p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-pulse-slow">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                            <i data-feather="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-orange-900 text-sm">Payment Details Missing</h4>
                            <p class="text-xs text-orange-700">You need to set up your bank account details before
                                requesting
                                a
                                payout.</p>
                        </div>
                    </div>
                    <a href="{{ route('staff.settings') }}"
                        class="px-4 py-2 bg-white border border-orange-200 text-orange-700 text-xs font-bold rounded-xl hover:bg-orange-600 hover:text-white transition-colors shadow-sm whitespace-nowrap">
                        Update Settings
                    </a>
                </div>
            @endif

            {{-- TOP STATS GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                {{-- 1. MAIN WALLET CARD --}}
                <div
                    class="bg-black text-white p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col justify-between group h-full min-h-[180px]">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-xs uppercase font-bold text-gray-400 tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Balance
                            </p>
                            <i data-feather="briefcase" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <h2 class="text-4xl font-bold tracking-tighter mb-4">{{ number_format($user->wallet->balance) }}
                            <span class="text-lg text-gray-600 font-medium">Token</span>
                        </h2>

                        <button @click="payoutModalOpen = true"
                            {{ empty($user->bank_account) || $user->wallet->balance <= 0 ? 'disabled' : '' }}
                            class="w-full py-3 bg-white text-black rounded-xl font-bold text-xs hover:bg-gray-200 disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 shadow-lg shadow-white/5 active:scale-95">
                            Request Payout <i data-feather="arrow-up-right" class="w-3 h-3"></i>
                        </button>
                    </div>

                    {{-- Decorations --}}
                    <div
                        class="absolute right-0 top-0 w-48 h-48 bg-gray-800 rounded-full opacity-30 blur-2xl -mr-10 -mt-10 pointer-events-none">
                    </div>
                    <div
                        class="absolute left-0 bottom-0 w-32 h-32 bg-blue-900 rounded-full opacity-40 blur-3xl -ml-10 -mb-10 pointer-events-none">
                    </div>
                    <svg class="absolute right-4 bottom-16 opacity-10" width="100" height="40" viewBox="0 0 100 40"
                        fill="none">
                        <path d="M0 40C30 40 30 0 60 0C90 0 90 40 120 40" stroke="white" stroke-width="2" />
                    </svg>
                </div>

                {{-- 2. CURRENT RATE CARD --}}
                <div
                    class="bg-gradient-to-br from-blue-600 to-blue-800 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden flex flex-col justify-center items-center text-center h-full min-h-[180px]">
                    <div class="relative z-10">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 border border-white/20">
                            <i data-feather="repeat" class="w-6 h-6 text-white"></i>
                        </div>
                        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-wider mb-1">Exchange Rate</p>
                        <p class="text-2xl font-bold">1 Token <span class="text-blue-300 mx-1">=</span> Rp
                            {{ number_format($rate, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-blue-200 mt-2 opacity-80">Rate is subject to agency policy</p>
                    </div>
                    <div
                        class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
                    </div>
                </div>

                {{-- 3. STATS COLUMN --}}
                <div class="flex flex-col gap-4 h-full">
                    {{-- Total Withdrawn --}}
                    <div
                        class="bg-white border border-gray-200 p-5 rounded-3xl shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 flex-1">
                        <div class="p-3 bg-green-50 rounded-xl border border-green-100 flex-shrink-0">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Withdrawn</p>
                            <p class="text-lg font-bold text-gray-900">Rp
                                {{ number_format($totalWithdrawn, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Pending Payout --}}
                    <div
                        class="bg-white border border-gray-200 p-5 rounded-3xl shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 flex-1">
                        <div class="p-3 bg-yellow-50 rounded-xl border border-yellow-100 flex-shrink-0">
                            <i data-feather="loader" class="w-5 h-5 text-yellow-600 animate-spin-slow"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Processing</p>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($pendingPayout, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HISTORY TABLES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- LEFT: WALLET ACTIVITY --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden h-fit">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="activity" class="w-4 h-4 text-gray-400"></i> Wallet Activity
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($transactions as $trx)
                            <div class="p-5 flex items-start justify-between hover:bg-gray-50/50 transition-colors group">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center border transition-colors
                                        {{ $trx->amount > 0
                                            ? 'bg-green-50 border-green-100 text-green-600 group-hover:border-green-200'
                                            : 'bg-gray-50 border-gray-200 text-gray-600 group-hover:border-gray-300' }}">
                                        <i data-feather="{{ $trx->amount > 0 ? 'arrow-down-left' : 'arrow-up-right' }}"
                                            class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-snug">{{ $trx->description }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 font-medium uppercase mt-1">
                                            {{ $trx->created_at->format('d M Y • H:i') }}</p>
                                    </div>
                                </div>
                                <span
                                    class="font-mono text-sm font-bold whitespace-nowrap ml-4 {{ $trx->amount > 0 ? 'text-green-600' : 'text-black' }}">
                                    {{ $trx->amount > 0 ? '+' : '' }}{{ $trx->amount }}
                                </span>
                            </div>
                        @empty
                            <div class="p-12 text-center text-gray-400">
                                <i data-feather="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-xs">No transactions yet</p>
                            </div>
                        @endforelse
                    </div>
                    @if ($transactions->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $transactions->appends(['payout_page' => request('payout_page')])->links() }}
                        </div>
                    @endif
                </div>

                {{-- RIGHT: WITHDRAWAL HISTORY --}}
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden h-fit">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="dollar-sign" class="w-4 h-4 text-gray-400"></i> Withdrawal History
                        </h3>
                    </div>

                    {{-- Tabel Structure --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400 font-bold tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-100">Request ID</th>
                                    <th class="px-6 py-3 border-b border-gray-100">Amount (IDR)</th>
                                    <th class="px-6 py-3 border-b border-gray-100">Status</th>
                                    <th class="px-6 py-3 border-b border-gray-100 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($payouts as $payout)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-xs text-gray-500">#PY-{{ $payout->id }}</span>
                                            <div class="text-[10px] text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($payout->created_at)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-gray-900 block">Rp
                                                {{ number_format($payout->amount_currency, 0, ',', '.') }}</span>
                                            <span
                                                class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-bold mt-1 inline-block">
                                                -{{ $payout->amount_token }} Tokens
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($payout->status == 'pending')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold uppercase border border-yellow-100">
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                                    Pending
                                                </span>
                                            @elseif($payout->status == 'approved')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-[10px] font-bold uppercase border border-green-100">
                                                    <i data-feather="check" class="w-3 h-3"></i> Paid
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-[10px] font-bold uppercase border border-red-100">
                                                    <i data-feather="x" class="w-3 h-3"></i> Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('staff.finance.show', $payout->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-400 hover:bg-black hover:text-white hover:border-black transition-all shadow-sm">
                                                <i data-feather="arrow-right" class="w-4 h-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <i data-feather="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                            <p class="text-xs">No withdrawals yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($payouts->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $payouts->appends(['trans_page' => request('trans_page')])->links() }}
                        </div>
                    @endif
                </div>

            </div>

        </div> {{-- END OF FADE-IN WRAPPER --}}

        {{-- INCLUDE MODAL DI LUAR FADE-IN --}}
        @include('staff.finance.payout')

    </div> {{-- END OF X-DATA WRAPPER --}}
@endsection
