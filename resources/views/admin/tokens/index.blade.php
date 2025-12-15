@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Token Manager</h1>
            <p class="text-gray-500 text-sm">Monitor sirkulasi token, riwayat transaksi, dan adjustment saldo.</p>
        </div>

        {{-- Button Trigger Modal --}}
        <button onclick="openAdjustModal()"
            class="px-5 py-2.5 bg-black text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20">
            <i data-feather="sliders" class="w-4 h-4"></i> Manual Adjustment
        </button>
    </div>

    {{-- STATS GRID (BENTO STYLE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        {{-- Card 1: Circulating (Hero) --}}
        <div class="bg-black text-white p-6 rounded-3xl relative overflow-hidden group shadow-xl shadow-black/10">
            <div
                class="absolute right-0 top-0 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-10 -mt-10 transition-all group-hover:scale-110">
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/10 rounded-xl backdrop-blur-sm">
                        <i data-feather="disc" class="w-5 h-5 text-white"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Circulating Supply</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold tracking-tight">{{ number_format($stats['circulating']) }} <span
                            class="text-lg text-gray-500">TX</span></h3>
                    <p class="text-[10px] text-gray-500 mt-1">Total tokens currently held by users</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Purchased --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-green-50 text-green-600 rounded-xl">
                        <i data-feather="trending-up" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lifetime Purchased</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_purchased']) }} <span
                            class="text-lg text-gray-400">TX</span></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Total tokens ever bought by clients</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Transactions Count --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <i data-feather="activity" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transactions</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['transactions_count']) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">Recorded ledger entries</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS & TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in flex flex-col">

        {{-- Toolbar --}}
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-white">
            <div class="flex gap-2 p-1 bg-gray-50 rounded-xl border border-gray-100">
                <a href="{{ route('admin.tokens.index') }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ !request('type') ? 'bg-white text-black shadow-sm' : 'text-gray-500 hover:text-black' }}">
                    All
                </a>
                <a href="{{ route('admin.tokens.index', ['type' => 'credit']) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('type') == 'credit' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-green-600' }}">
                    Incoming
                </a>
                <a href="{{ route('admin.tokens.index', ['type' => 'debit']) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('type') == 'debit' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-red-600' }}">
                    Outgoing
                </a>
            </div>

            <form action="{{ route('admin.tokens.index') }}" method="GET" class="relative group w-full md:w-72">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or email..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-xs font-bold focus:outline-none focus:border-black focus:bg-white transition-all placeholder-gray-400">
                <i data-feather="search"
                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 group-focus-within:text-black transition-colors"></i>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 tracking-wider">User / Wallet</th>
                        <th class="px-6 py-4 tracking-wider">Label</th>
                        <th class="px-6 py-4 tracking-wider">Description</th>
                        <th class="px-6 py-4 tracking-wider text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($transactions as $trx)
                        @php
                            $descLower = Str::lower($trx->description);
                            $dbTypeIsCredit = Str::lower($trx->type) === 'credit';

                            // 1. LOGIKA DETEKSI TIPE TRANSAKSI (Overrides DB Type jika perlu)
                            // Jika deskripsi mengandung keyword tertentu, kita paksa anggap sebagai CREDIT (Masuk)
                            if (Str::contains($descLower, ['top up', 'topup', 'deposit', 'bonus', 'refund'])) {
                                $isCredit = true;
                            } else {
                                // Fallback ke tipe asli database
                                $isCredit = $dbTypeIsCredit;
                            }

                            // 2. LOGIKA LABEL & WARNA
                            if (Str::contains($descLower, 'refund')) {
                                $label = 'Refund';
                                $labelColor = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (Str::contains($descLower, ['top up', 'topup', 'deposit'])) {
                                $label = 'Top Up';
                                $labelColor = 'bg-green-50 text-green-700 border-green-200';
                            } elseif (Str::contains($descLower, 'bonus')) {
                                $label = 'Bonus';
                                $labelColor = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                            } elseif (Str::contains($descLower, 'adjustment')) {
                                $label = 'Adjustment';
                                $labelColor = 'bg-purple-50 text-purple-700 border-purple-200';
                            } else {
                                // Default Labels
                                $label = $isCredit ? 'Credit' : 'Usage';
                                $labelColor = $isCredit
                                    ? 'bg-gray-100 text-gray-700 border-gray-200'
                                    : 'bg-gray-100 text-gray-600 border-gray-200';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            {{-- Date --}}
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i:s') }}</p>
                            </td>

                            {{-- User --}}
                            <td class="px-6 py-4">
                                @if ($trx->wallet && $trx->wallet->user)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                            <img src="{{ $trx->wallet->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($trx->wallet->user->full_name) }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">
                                                {{ Str::limit($trx->wallet->user->full_name, 20) }}</p>
                                            <p class="text-[10px] text-gray-400">Bal:
                                                {{ number_format($trx->wallet->balance) }} TX</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unknown Wallet</span>
                                @endif
                            </td>

                            {{-- Label --}}
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $labelColor }}">
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4">
                                <p class="text-gray-600 font-medium text-xs">{{ $trx->description }}</p>
                            </td>

                            {{-- Amount --}}
                            <td class="px-6 py-4 text-right">
                                @if ($isCredit)
                                    {{-- Hijau untuk Credit (Topup, Bonus, Refund) --}}
                                    <span class="font-bold text-green-600 flex items-center justify-end gap-1">
                                        +{{ number_format($trx->amount) }} <i data-feather="arrow-up-right"
                                            class="w-3 h-3"></i>
                                    </span>
                                @else
                                    {{-- Merah untuk Debit (Usage) --}}
                                    <span class="font-bold text-red-600 flex items-center justify-end gap-1">
                                        -{{ number_format($trx->amount) }} <i data-feather="arrow-down-right"
                                            class="w-3 h-3"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-3 bg-gray-50 rounded-full">
                                        <i data-feather="list" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Tidak ada transaksi ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL ADJUSTMENT (BENTO STYLE) --}}
    <div id="adjustModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeAdjustModal()">
        </div>

        {{-- Modal Content --}}
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl transform transition-all scale-100">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Manual Adjustment</h3>
                    <p class="text-sm text-gray-500">Tambah atau kurangi saldo user secara manual.</p>
                </div>
                <button onclick="closeAdjustModal()"
                    class="p-2 bg-gray-50 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.tokens.adjust') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Select User --}}
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Target User</label>
                    <select name="user_id" required
                        class="w-full bg-transparent border-none p-0 focus:ring-0 text-sm font-bold text-gray-900 cursor-pointer">
                        <option value="" disabled selected>Select a client...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Type --}}
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="credit" class="peer sr-only" checked>
                        <div
                            class="p-4 rounded-2xl border border-gray-200 bg-white peer-checked:bg-green-50 peer-checked:border-green-200 peer-checked:text-green-700 transition-all text-center group hover:bg-gray-50">
                            <i data-feather="plus-circle"
                                class="w-6 h-6 mx-auto mb-2 text-gray-400 group-peer-checked:text-green-600"></i>
                            <span class="text-xs font-bold block">Add Balance</span>
                            <span class="text-[9px] uppercase tracking-wide opacity-60">Credit</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="debit" class="peer sr-only">
                        <div
                            class="p-4 rounded-2xl border border-gray-200 bg-white peer-checked:bg-red-50 peer-checked:border-red-200 peer-checked:text-red-700 transition-all text-center group hover:bg-gray-50">
                            <i data-feather="minus-circle"
                                class="w-6 h-6 mx-auto mb-2 text-gray-400 group-peer-checked:text-red-600"></i>
                            <span class="text-xs font-bold block">Deduct Balance</span>
                            <span class="text-[9px] uppercase tracking-wide opacity-60">Debit</span>
                        </div>
                    </label>
                </div>

                {{-- Amount & Reason --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Amount (Token)</label>
                        <input type="number" name="amount" placeholder="e.g. 50" min="1" required
                            class="w-full font-bold bg-transparent border-none p-0 focus:ring-0 text-gray-900 text-lg">
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Reason</label>
                        <input type="text" name="description" placeholder="e.g. Bonus" required
                            class="w-full font-medium bg-transparent border-none p-0 focus:ring-0 text-gray-900 text-sm">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-4 rounded-xl bg-black text-white font-bold text-sm hover:bg-gray-800 shadow-xl shadow-black/20 transition-all flex items-center justify-center gap-2">
                        <i data-feather="check" class="w-4 h-4"></i> Confirm Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function openAdjustModal() {
            document.getElementById('adjustModal').classList.remove('hidden');
        }

        function closeAdjustModal() {
            document.getElementById('adjustModal').classList.add('hidden');
        }
    </script>
@endsection
