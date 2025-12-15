@extends('staff.layouts.app')

@section('content')
    <div class="fade-in pb-20" x-data="{ payoutModalOpen: false, tokenInput: 0, rate: {{ $rate }} }">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Earnings & Payouts</h1>
            <p class="text-gray-500 mt-1">Manage your wallet, track income, and withdraw your earnings.</p>
        </div>

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
                        <p class="text-xs text-orange-700">You need to set up your bank account details before requesting a
                            payout.</p>
                    </div>
                </div>
                <a href="{{ route('staff.settings') }}"
                    class="px-4 py-2 bg-white border border-orange-200 text-orange-700 text-xs font-bold rounded-xl hover:bg-orange-600 hover:text-white transition-colors shadow-sm whitespace-nowrap">
                    Update Settings
                </a>
            </div>
        @endif

        {{-- TOP STATS GRID (Revised Layout) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            {{-- 1. MAIN WALLET CARD (More Compact & Decorated) --}}
            <div
                class="bg-black text-white p-6 rounded-3xl shadow-xl relative overflow-hidden flex flex-col justify-between group h-full min-h-[180px]">
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-xs uppercase font-bold text-gray-400 tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Balance
                        </p>
                        <i data-feather="briefcase" class="w-5 h-5 text-gray-500"></i>
                    </div>
                    <h2 class="text-4xl font-bold tracking-tighter mb-4">{{ number_format($user->wallet->balance) }} <span
                            class="text-lg text-gray-600 font-medium">Token</span></h2>

                    <button @click="payoutModalOpen = true"
                        {{ empty($user->bank_account) || $user->wallet->balance <= 0 ? 'disabled' : '' }}
                        class="w-full py-3 bg-white text-black rounded-xl font-bold text-xs hover:bg-gray-200 disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 shadow-lg shadow-white/5 active:scale-95">
                        Request Payout <i data-feather="arrow-up-right" class="w-3 h-3"></i>
                    </button>
                </div>

                {{-- Decorations to fill empty space --}}
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

            {{-- 2. CURRENT RATE CARD (Moved Here) --}}
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
                {{-- Decoration --}}
                <div
                    class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
                </div>
            </div>

            {{-- 3. STATS COLUMN (Horizontal Layouts) --}}
            <div class="flex flex-col gap-4 h-full">
                {{-- Total Withdrawn --}}
                <div
                    class="bg-white border border-gray-200 p-5 rounded-3xl shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 flex-1">
                    <div class="p-3 bg-green-50 rounded-xl border border-green-100 flex-shrink-0">
                        <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total Withdrawn</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</p>
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
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($pendingPayout, 0, ',', '.') }}</p>
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
                                    {{-- UPDATE: Menghapus Str::limit agar teks tampil penuh --}}
                                    <p class="text-sm font-bold text-gray-900 leading-snug">{{ $trx->description }}</p>
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
                <div class="divide-y divide-gray-100">
                    @forelse($payouts as $payout)
                        <div class="p-5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div>
                                <p class="text-sm font-bold text-gray-900">IDR
                                    {{ number_format($payout->amount_currency, 0, ',', '.') }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payout->created_at)->format('d M Y') }}</span>
                                    <span
                                        class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-bold">{{ $payout->amount_token }}
                                        Token</span>
                                </div>
                            </div>
                            <div>
                                @if ($payout->status == 'pending')
                                    <span
                                        class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 text-[10px] font-bold uppercase border border-yellow-200">Pending</span>
                                @elseif($payout->status == 'paid')
                                    <span
                                        class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-[10px] font-bold uppercase border border-green-200">Paid</span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-[10px] font-bold uppercase border border-red-200">Rejected</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-400">
                            <i data-feather="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-xs">No withdrawals yet</p>
                        </div>
                    @endforelse
                </div>
                @if ($payouts->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $payouts->appends(['trans_page' => request('trans_page')])->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- PAYOUT CONFIRMATION MODAL --}}
        <div x-show="payoutModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 fade-in">

            <div @click.away="payoutModalOpen = false"
                class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">

                {{-- Modal Header --}}
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                        <i data-feather="arrow-up-right" class="w-5 h-5"></i> Request Payout
                    </h3>
                    <button @click="payoutModalOpen = false"
                        class="text-gray-400 hover:text-black hover:bg-gray-200 p-1 rounded-full transition-colors">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('staff.finance.payout') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-6">

                        {{-- Destination Details --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                            <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider mb-2">Destination
                                Account</p>
                            @if ($user->bank_name && $user->bank_account)
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-white rounded-lg text-blue-600 shadow-sm">
                                        <i data-feather="credit-card" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $user->bank_name }}</p>
                                        <p class="text-sm font-mono text-gray-600">{{ $user->bank_account }}</p>
                                        <p class="text-xs text-gray-500 uppercase mt-0.5">{{ $user->bank_holder }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-red-600">
                                    <i data-feather="alert-circle" class="w-4 h-4"></i>
                                    <span class="text-sm font-bold">No bank details found.</span>
                                </div>
                            @endif
                        </div>

                        {{-- Input Token --}}
                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-xs font-bold text-gray-500 uppercase">Amount to withdraw</label>
                                <span class="text-xs text-gray-400">Max: {{ $user->wallet->balance }}</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="token_amount" x-model="tokenInput" min="1"
                                    max="{{ $user->wallet->balance }}"
                                    class="w-full text-3xl font-bold p-4 pr-16 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all placeholder-gray-300"
                                    placeholder="0">
                                <span
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">Tokens</span>
                            </div>
                        </div>

                        {{-- Conversion Result --}}
                        <div class="p-5 bg-black rounded-2xl text-white shadow-lg">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total
                                        Estimated (IDR)</p>
                                    <p class="text-2xl font-bold font-mono text-white">
                                        Rp <span x-text="(tokenInput * rate).toLocaleString('id-ID')">0</span>
                                    </p>
                                </div>
                                <i data-feather="check-circle" class="w-8 h-8 text-green-500 opacity-50"></i>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="p-6 pt-0">
                        <button type="submit"
                            :disabled="tokenInput <= 0 || tokenInput > {{ $user->wallet->balance }} || !
                                {{ $user->bank_account ? 'true' : 'false' }}"
                            class="w-full py-4 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed transition-all shadow-lg active:translate-y-0.5">
                            Confirm Payout
                        </button>
                        <p class="text-center text-[10px] text-gray-400 mt-3">
                            Funds will be transferred within 24-48 hours after approval.
                        </p>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
