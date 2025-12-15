@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Payroll & Performance</h1>
            <p class="text-gray-500 text-sm">Kelola pengajuan gaji staff, pantau kinerja, dan riwayat pembayaran.</p>
        </div>
        <div class="flex gap-2">
            <button
                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-1"></i> Export Report
            </button>
        </div>
    </div>

    {{-- 1. SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">
        {{-- Card 1: Pending Requests --}}
        <div class="bg-yellow-50 p-6 rounded-3xl border border-yellow-100 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-yellow-100 text-yellow-700 rounded-xl"><i data-feather="clock" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-yellow-800 uppercase tracking-wider">Pending Payouts</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_tx']) }} <span
                            class="text-lg text-gray-500">TX</span></h3>
                    <p class="text-xs text-yellow-700 mt-1 font-medium">{{ $stats['pending_count'] }} requests waiting</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Paid --}}
        <div class="bg-green-50 p-6 rounded-3xl border border-green-100 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-green-100 text-green-700 rounded-xl"><i data-feather="check-circle"
                            class="w-5 h-5"></i></div>
                    <p class="text-xs font-bold text-green-800 uppercase tracking-wider">Total Paid (IDR)</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">Rp
                        {{ number_format($stats['total_paid_idr'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-green-700 mt-1 font-medium">Successfully transferred</p>
                </div>
            </div>
        </div>

        {{-- Card 3: PAYOUT RATE (EDITABLE) --}}
        <div onclick="openRateModal()"
            class="bg-blue-50 p-6 rounded-3xl border border-blue-100 relative overflow-hidden cursor-pointer hover:shadow-md transition-all group">
            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="p-2 bg-white rounded-full shadow-sm text-blue-600"><i data-feather="edit-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-xl"><i data-feather="settings" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wider">Staff Payout Rate</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($stats['current_rate'], 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-blue-700 mt-1 font-medium">per 1 Toratix Token</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TABEL PENDING PAYOUT (Sama seperti sebelumnya) --}}
    @if ($pendingPayouts->count() > 0)
        <div
            class="bg-white rounded-3xl border border-yellow-200 shadow-lg shadow-yellow-100/50 overflow-hidden mb-10 fade-in">
            {{-- ... Isi Tabel Pending Payout biarkan sama ... --}}
            <div class="px-8 py-6 border-b border-yellow-100 bg-yellow-50/30 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </span>
                    Payout Requests Needed
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-yellow-50/10 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4">Request Date</th>
                            <th class="px-8 py-4">Staff Name</th>
                            <th class="px-8 py-4">Requested</th>
                            <th class="px-8 py-4">Estimation (IDR)</th>
                            <th class="px-8 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach ($pendingPayouts as $payout)
                            <tr class="hover:bg-yellow-50/20 transition-colors">
                                <td class="px-8 py-4 font-mono text-xs text-gray-500">
                                    {{ $payout->created_at->format('d M, H:i') }}</td>
                                <td class="px-8 py-4 font-bold text-gray-900">{{ $payout->user->full_name }}</td>
                                <td class="px-8 py-4 font-bold">{{ number_format($payout->amount_token) }} TX</td>
                                <td class="px-8 py-4 text-gray-500">~ Rp
                                    {{ number_format($payout->amount_token * $stats['current_rate'], 0, ',', '.') }}</td>
                                <td class="px-8 py-4 text-right">
                                    <button
                                        onclick="openApproveModal('{{ $payout->id }}', '{{ $payout->user->full_name }}', '{{ $payout->amount_token }}', {{ $stats['current_rate'] }})"
                                        class="px-4 py-2 bg-black text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-black/20">
                                        Process
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- 3. STAFF PERFORMANCE TABLE (LEADERBOARD) --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden mb-10 fade-in">
        <div class="px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="font-bold text-lg text-gray-900">Staff Performance Leaderboard</h3>
            <form action="{{ route('admin.performance.index') }}" method="GET" class="relative w-full md:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search staff..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-xs font-bold focus:outline-none focus:border-black focus:bg-white transition-all">
                <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">Rank</th> {{-- KOLOM RANK --}}
                        <th class="px-6 py-4">Staff Name</th>
                        <th class="px-6 py-4 text-center">Projects Done</th>
                        <th class="px-6 py-4 text-right">Total Payout (IDR)</th>
                        <th class="px-6 py-4 text-right">Unpaid Balance</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach ($staffs as $index => $staff)
                        @php
                            // Hitung Ranking Absolute (Memperhitungkan Pagination)
                            $rank = ($staffs->currentPage() - 1) * $staffs->perPage() + $loop->iteration;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- KOLOM RANKING DENGAN BADGE --}}
                            <td class="px-6 py-4 text-center">
                                @if ($rank == 1)
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 1">
                                        <i data-feather="award" class="w-4 h-4"></i>
                                    </div>
                                @elseif($rank == 2)
                                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 border border-gray-300 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 2">
                                        2
                                    </div>
                                @elseif($rank == 3)
                                    <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 3">
                                        3
                                    </div>
                                @else
                                    <span class="font-bold text-gray-400">{{ $rank }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $staff->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($staff->full_name) }}"
                                        class="w-8 h-8 rounded-full bg-gray-100 object-cover border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $staff->full_name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $staff->role }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-700">{{ $staff->completed_tasks_count }}</span>
                            </td>
                            {{-- URUTAN BERDASARKAN INI --}}
                            <td class="px-6 py-4 text-right">
                                <span class="font-mono font-bold text-green-600">
                                    Rp {{ number_format($staff->total_payout_idr ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="font-mono font-bold text-gray-900">{{ number_format($staff->wallet->balance ?? 0) }}
                                    TX</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.performance.show', $staff->id) }}"
                                    class="p-2 bg-gray-100 rounded-lg hover:bg-black hover:text-white transition-colors">
                                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($staffs->hasPages())
            <div class="px-8 py-4 border-t border-gray-100">{{ $staffs->appends(['search' => $search])->links() }}</div>
        @endif
    </div>

    {{-- 4. PAYOUT HISTORY (GLOBAL) --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden mb-10 fade-in">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                <i data-feather="file-text" class="w-5 h-5 text-gray-400"></i> Global Payout History
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-white text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-4">Processed Date</th>
                        <th class="px-8 py-4">Staff Name</th>
                        <th class="px-8 py-4">Amount (Token)</th>
                        <th class="px-8 py-4">Amount (IDR)</th>
                        <th class="px-8 py-4 text-center">Status</th>
                        <th class="px-8 py-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($payoutHistory as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-4 text-gray-500 text-xs">
                                {{ $history->updated_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-8 py-4 font-bold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $history->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($history->user->full_name) }}"
                                        class="w-6 h-6 rounded-full border border-gray-200">
                                    {{ $history->user->full_name }}
                                </div>
                            </td>
                            <td class="px-8 py-4 text-gray-500">
                                {{ number_format($history->amount_token) }} TX
                            </td>
                            <td class="px-8 py-4 font-mono font-bold text-gray-900">
                                Rp {{ number_format($history->amount_currency, 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-4 text-center">
                                <span
                                    class="px-2 py-0.5 bg-green-50 text-green-600 rounded-md text-[10px] font-bold uppercase border border-green-100">
                                    Paid
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <button class="text-gray-400 hover:text-black transition-colors"
                                    title="Download PDF (Dummy)">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-10 text-center text-gray-400 text-xs">
                                No payout history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL 1: EDIT RATE PAYOUT --}}
    <div id="rateModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeRateModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white rounded-3xl p-8 shadow-2xl transform transition-all scale-100">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Payout Rate Config</h3>
            <p class="text-xs text-gray-500 mb-6">Tentukan nilai tukar 1 Token ke Rupiah untuk gaji staff.</p>

            <form action="{{ route('admin.performance.update_rate') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Rate per 1 Token (IDR)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="number" name="rate_per_token" value="{{ $stats['current_rate'] }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-3 font-bold text-gray-900 focus:outline-none focus:border-blue-500 focus:bg-white transition-all text-lg">
                    </div>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/30">
                    Save New Rate
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL 2: APPROVE PAYOUT --}}
    <div id="approveModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeApproveModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl p-8 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Confirm Transfer</h3>
            <p class="text-xs text-gray-500 mb-6">Masukkan nominal yang <b>sebenarnya</b> ditransfer.</p>

            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-4 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Token Amount</p>
                        <p class="font-bold text-lg text-black" id="modalTokenAmount">0 TX</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-gray-400">System Est.</p>
                        <p class="font-bold text-sm text-gray-500" id="modalEstAmount">Rp 0</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block">Real Transfer Amount
                        (IDR)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="number" name="amount_idr" id="modalInputAmount" required
                            class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 font-bold text-gray-900 focus:outline-none focus:border-black transition-all">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeApproveModal()"
                        class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-xs hover:bg-gray-800 shadow-lg shadow-black/20">Confirm
                        Paid</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- RATE MODAL ---
        function openRateModal() {
            document.getElementById('rateModal').classList.remove('hidden');
        }

        function closeRateModal() {
            document.getElementById('rateModal').classList.add('hidden');
        }

        // --- APPROVE MODAL ---
        function openApproveModal(id, name, token, rate) {
            document.getElementById('approveModal').classList.remove('hidden');

            document.getElementById('modalTokenAmount').innerText = parseInt(token).toLocaleString() + ' TX';

            // Hitung estimasi rupiah otomatis berdasarkan rate saat ini
            let estIDR = token * rate;
            document.getElementById('modalEstAmount').innerText = 'Rp ' + estIDR.toLocaleString();
            document.getElementById('modalInputAmount').value = estIDR; // Auto fill input

            let url = "{{ route('admin.performance.approve', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('approveForm').action = url;
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }
    </script>
@endsection
