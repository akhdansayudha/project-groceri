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

    {{-- 2. TABEL PENDING PAYOUT (Updated Design & Columns) --}}
    @if ($pendingPayouts->count() > 0)
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden mb-10 fade-in">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-white">
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
                    <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Request Info</th>
                            <th class="px-6 py-4">Staff Details</th>
                            <th class="px-6 py-4">Destination Bank</th>
                            <th class="px-6 py-4 text-right">Amount (IDR)</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach ($pendingPayouts as $payout)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-mono text-xs font-bold text-gray-900">#PY-{{ $payout->id }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">
                                        {{ $payout->created_at->format('d M Y, H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $payout->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($payout->user->full_name) }}"
                                            class="w-8 h-8 rounded-full bg-gray-100 object-cover border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $payout->user->full_name }}</p>
                                            <p class="text-[10px] text-gray-500 font-mono">
                                                {{ number_format($payout->amount_token) }}
                                                TX</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{-- Logic Bank: Prioritas dari data payout (snapshot), fallback ke data user --}}
                                    @php
                                        $bankName = $payout->bank_name ?? ($payout->user->bank_name ?? '-');
                                        $bankAcc = $payout->bank_account ?? ($payout->user->bank_account ?? '-');
                                        $bankHolder = $payout->bank_holder ?? ($payout->user->bank_holder ?? '-');
                                    @endphp
                                    <div>
                                        <p class="font-bold text-xs text-gray-900">{{ $bankName }}</p>
                                        <p class="font-mono text-xs text-gray-600">{{ $bankAcc }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase">{{ Str::limit($bankHolder, 15) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="font-bold text-gray-900">
                                        Rp
                                        {{ number_format($payout->amount_token * $stats['current_rate'], 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">Est. Value</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{-- Mengirim object full payout ke fungsi JS --}}
                                    <button
                                        onclick='openTransferModal(@json($payout), @json($payout->user))'
                                        class="px-4 py-2 bg-black text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm">
                                        Process
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($pendingPayouts->hasPages())
                <div class="px-8 py-4 border-t border-gray-100">
                    {{ $pendingPayouts->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- 3. STAFF PERFORMANCE LEADERBOARD --}}
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
                        <th class="px-6 py-4 w-16 text-center">Rank</th>
                        <th class="px-6 py-4">Staff Name</th>
                        <th class="px-6 py-4 text-center">Projects Done</th>
                        <th class="px-6 py-4 text-right">Total Payout (IDR)</th>
                        <th class="px-6 py-4 text-right">Unpaid Balance</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach ($staffs as $staff)
                        @php
                            $rank = ($staffs->currentPage() - 1) * $staffs->perPage() + $loop->iteration;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                @if ($rank == 1)
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 1">
                                        <i data-feather="award" class="w-4 h-4"></i>
                                    </div>
                                @elseif($rank == 2)
                                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 border border-gray-300 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 2">2</div>
                                @elseif($rank == 3)
                                    <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center font-bold mx-auto shadow-sm"
                                        title="Top 3">3</div>
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
                                {{-- Mengambil dari withCount di controller --}}
                                <span class="font-bold text-gray-700">{{ $staff->completed_tasks_count ?? 0 }}</span>
                            </td>
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
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-black hover:text-white transition-all">
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

    {{-- 4. GLOBAL PAYOUT HISTORY (Updated Columns & Modal) --}}
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
                        <th class="px-6 py-4">ID & Date</th>
                        <th class="px-6 py-4">Staff Name</th>
                        <th class="px-6 py-4">Bank Details</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($payoutHistory as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 font-mono">#PY-{{ $history->id }}</p>
                                <div class="mt-1">
                                    <p class="text-[10px] text-gray-400">Req:
                                        {{ $history->created_at->format('d M, H:i') }}</p>
                                    <p class="text-[10px] text-green-600 font-medium">Proc:
                                        {{ $history->updated_at->format('d M, H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $history->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($history->user->full_name) }}"
                                        class="w-6 h-6 rounded-full border border-gray-200">
                                    {{ $history->user->full_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    // Logika Fallback: Cek data di history (snapshot) dulu, baru ke data user
                                    $hBank = $history->bank_name ?? ($history->user->bank_name ?? '-');
                                    $hAcc = $history->bank_account ?? ($history->user->bank_account ?? '-');
                                    $hHolder = $history->bank_holder ?? ($history->user->bank_holder ?? '-'); // Ambil Nama Pemilik
                                @endphp
                                <p class="text-xs font-bold text-gray-800">{{ $hBank }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $hAcc }}</p>
                                <p class="text-[10px] text-gray-400 uppercase mt-0.5 font-bold tracking-wide">
                                    {{ $hHolder }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-gray-900">
                                    Rp {{ number_format($history->amount_currency, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-gray-500">{{ number_format($history->amount_token) }} TX</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($history->status == 'approved')
                                    <span
                                        class="px-2 py-0.5 bg-green-50 text-green-600 rounded-md text-[10px] font-bold uppercase border border-green-100">
                                        Paid
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-0.5 bg-red-50 text-red-600 rounded-md text-[10px] font-bold uppercase border border-red-100">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($history->status == 'approved' && $history->proof_url)
                                    {{-- Gunakan helper Storage::disk('supabase')->url() untuk generate full link --}}
                                    <button
                                        onclick="openReceiptModal('{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($history->proof_url) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[10px] font-bold text-gray-700 hover:bg-gray-50 hover:text-black transition-colors shadow-sm">
                                        <i data-feather="file-text" class="w-3 h-3"></i> View
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-xs">
                                No payout history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payoutHistory->hasPages())
            <div class="px-8 py-4 border-t border-gray-100">{{ $payoutHistory->appends(['search' => $search])->links() }}
            </div>
        @endif
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

    {{-- MODAL 2: RECEIPT VIEWER (NEW) --}}
    <div id="receiptModal" class="fixed inset-0 z-[60] hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-w-2xl w-full p-4">
            <div class="relative bg-transparent rounded-2xl overflow-hidden shadow-2xl">
                <button onclick="closeReceiptModal()"
                    class="absolute top-4 right-4 bg-black/50 hover:bg-black text-white p-2 rounded-full transition-colors backdrop-blur-md z-10">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
                <img id="receiptImage" src=""
                    class="w-full h-auto max-h-[85vh] object-contain rounded-2xl bg-white">
            </div>
        </div>
    </div>

    {{-- Sertakan Modal Transfer dari file transfer.blade.php jika include, atau salin kode modalnya kesini --}}
    {{-- Assuming transfer.blade.php is included via include or manually pasted below. 
         Jika Anda menggunakan @include('admin.performance.transfer'), biarkan saja. 
         Di sini saya asumsikan User ingin file index ini berdiri sendiri atau include modal transfer di footer layout.
         Untuk keamanan, pastikan JS `openTransferModal` tersedia. --}}

    @include('admin.performance.transfer') {{-- Asumsi file modal transfer dipisah --}}

    <script>
        // --- RATE MODAL ---
        function openRateModal() {
            document.getElementById('rateModal').classList.remove('hidden');
        }

        function closeRateModal() {
            document.getElementById('rateModal').classList.add('hidden');
        }

        // --- RECEIPT MODAL ---
        function openReceiptModal(url) {
            const modal = document.getElementById('receiptModal');
            const img = document.getElementById('receiptImage');

            img.src = url;
            modal.classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
            setTimeout(() => {
                document.getElementById('receiptImage').src = '';
            }, 300);
        }
    </script>
@endsection
