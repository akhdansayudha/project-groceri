@extends('admin.layouts.app')

@section('content')
    {{-- WRAPPER UTAMA DENGAN ALPINE DATA --}}
    <div x-data="{
        payoutModalOpen: false,
        rate: {{ $currentRate }},
        maxBalance: {{ $stats['current_balance'] }},
        tokenInput: {{ $stats['current_balance'] }}
    }">

        {{-- Header Navigation --}}
        <div class="mb-8 fade-in flex items-center gap-4">
            <a href="{{ route('admin.performance.index') }}"
                class="p-2.5 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Performance Report</h1>
                <p class="text-gray-500 text-xs mt-0.5">Detail kinerja dan payroll untuk {{ $staff->full_name }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 flex items-center gap-3">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

            {{-- KOLOM KIRI: PROFILE & WALLET --}}
            <div class="space-y-6">
                {{-- Profile Summary --}}
                <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm text-center relative overflow-hidden">
                    <div class="relative inline-block mb-4">
                        <img src="{{ $staff->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($staff->full_name) }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover mx-auto relative z-10">
                        <div class="absolute inset-0 bg-gray-100 rounded-full blur-xl opacity-50 z-0"></div>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $staff->full_name }}</h2>
                    <p class="text-sm text-gray-500 mb-4">{{ $staff->email }}</p>

                    @if ($staff->bank_name)
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-left mb-4">
                            <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Bank Account</p>
                            <p class="text-xs font-bold text-gray-800">{{ $staff->bank_name }} - {{ $staff->bank_account }}
                            </p>
                            <p class="text-[10px] text-gray-500">{{ $staff->bank_holder }}</p>
                        </div>
                    @else
                        <div class="bg-red-50 p-3 rounded-xl border border-red-100 text-center mb-4">
                            <p class="text-xs text-red-600 font-bold flex items-center justify-center gap-1">
                                <i data-feather="alert-circle" class="w-3 h-3"></i> Bank info missing
                            </p>
                        </div>
                    @endif
                </div>

                {{-- CARD: TOTAL PAID OUT --}}
                <div class="bg-emerald-600 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="p-1.5 bg-emerald-500/50 rounded-lg">
                                <i data-feather="check-circle" class="w-4 h-4 text-white"></i>
                            </div>
                            <p class="text-xs font-bold text-emerald-100 uppercase tracking-widest">Total Paid Out</p>
                        </div>
                        <h3 class="text-3xl font-bold tracking-tight">
                            {{ number_format($stats['total_payout_tokens']) }} <span
                                class="text-lg text-emerald-200">TX</span>
                        </h3>
                        <p
                            class="text-xs text-emerald-100 mt-2 opacity-80 font-mono bg-emerald-700/50 inline-block px-2 py-1 rounded-lg">
                            Total: Rp {{ number_format($stats['total_payout_idr'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white rounded-full blur-3xl opacity-10 -mr-10 -mt-10">
                    </div>
                </div>

                {{-- Wallet & Payout Action --}}
                <div class="bg-black text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Unpaid Balance</p>
                                <h3 class="text-4xl font-bold tracking-tight">
                                    {{ number_format($stats['current_balance']) }}
                                    <span class="text-lg text-gray-500">TX</span>
                                </h3>
                            </div>
                            <div class="p-2.5 bg-white/10 rounded-xl group-hover:bg-white/20 transition-colors">
                                <i data-feather="briefcase" class="w-6 h-6"></i>
                            </div>
                        </div>

                        {{-- TOMBOL TRIGGER MODAL --}}
                        <button @click="payoutModalOpen = true" :disabled="maxBalance <= 0"
                            class="w-full py-3.5 bg-white text-black rounded-xl font-bold text-sm hover:bg-gray-200 disabled:bg-gray-800 disabled:text-gray-500 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 group/btn shadow-lg">
                            <i data-feather="dollar-sign"
                                class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                            Process Payout
                        </button>
                        <p class="text-[10px] text-gray-500 mt-3 text-center">Klik untuk memproses pembayaran manual.</p>
                    </div>
                    {{-- Decorative Blob --}}
                    <div
                        class="absolute right-0 bottom-0 w-40 h-40 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-10 -mb-10">
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: STATS & PROJECT TABLE --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Stats Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:border-black/20 transition-colors">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i data-feather="layers"
                                    class="w-4 h-4"></i></div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Active</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_tasks'] }}</p>
                    </div>
                    <div
                        class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:border-black/20 transition-colors">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-green-50 text-green-600 rounded-lg"><i data-feather="check-circle"
                                    class="w-4 h-4"></i></div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Completed</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_tasks'] }}</p>
                    </div>
                    <div
                        class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:border-black/20 transition-colors">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg"><i data-feather="award"
                                    class="w-4 h-4"></i></div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Lifetime</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_earned']) }} <span
                                class="text-xs font-normal text-gray-400">TX</span></p>
                    </div>
                    {{-- Performance Grade Card --}}
                    <div
                        class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm hover:border-black/20 transition-colors">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                                <i data-feather="trending-up" class="w-4 h-4"></i>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Performance Grade</p>
                        </div>

                        @if ($stats['completed_tasks'] == 0)
                            <div class="text-2xl font-bold text-gray-400">No Data</div>
                        @elseif($onTimeRate >= 90)
                            <div class="text-2xl font-bold text-green-600">A+ <span
                                    class="text-xs text-gray-400 font-normal">Outstanding</span></div>
                        @elseif($onTimeRate >= 80)
                            <div class="text-2xl font-bold text-blue-600">B <span
                                    class="text-xs text-gray-400 font-normal">Solid</span></div>
                        @else
                            <div class="text-2xl font-bold text-yellow-600">C <span
                                    class="text-xs text-gray-400 font-normal">Developing</span></div>
                        @endif
                    </div>
                </div>

                {{-- SECTION BARU: SERVICE BREAKDOWN (DIKEMBALIKAN) --}}
                @if (count($serviceStats) > 0)
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-feather="pie-chart" class="w-4 h-4 text-gray-400"></i> Completed Projects Breakdown
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($serviceStats as $stat)
                                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-white flex items-center justify-center border border-gray-200 text-gray-500 shadow-sm">
                                        @if ($stat->icon_url)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($stat->icon_url) }}"
                                                class="w-5 h-5 object-contain">
                                        @else
                                            <i data-feather="box" class="w-5 h-5"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">{{ $stat->name }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $stat->total }} projects completed</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Task History Table --}}
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="list" class="w-4 h-4 text-gray-400"></i> Task History
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-white text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Project</th>
                                    <th class="px-6 py-4">Client</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Comm.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($projects as $task)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <a href="{{ route('admin.projects.show', $task->id) }}"
                                                class="font-bold text-gray-900 hover:underline block mb-0.5">
                                                {{ Str::limit($task->title, 30) }}
                                            </a>
                                            <span class="text-xs text-gray-500">{{ $task->service->name ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded-full bg-gray-200 overflow-hidden">
                                                    <img src="{{ $task->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($task->user->full_name) }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                {{ Str::limit($task->user->full_name, 15) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $badgeClass = match ($task->status) {
                                                    'completed' => 'bg-green-50 text-green-700 border border-green-100',
                                                    'active',
                                                    'in_progress'
                                                        => 'bg-blue-50 text-blue-700 border border-blue-100',
                                                    'review',
                                                    'revision'
                                                        => 'bg-purple-50 text-purple-700 border border-purple-100',
                                                    'cancelled' => 'bg-red-50 text-red-700 border border-red-100',
                                                    default => 'bg-gray-50 text-gray-600 border border-gray-200',
                                                };
                                            @endphp
                                            <span
                                                class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase {{ $badgeClass }}">
                                                {{ str_replace('_', ' ', $task->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if ($task->status == 'completed')
                                                {{-- UPDATE DI SINI: Gunakan toratix_locked sesuai request --}}
                                                <span class="font-bold text-green-600 flex items-center justify-end gap-1">
                                                    +{{ $task->toratix_locked ?? 0 }} <i data-feather="check"
                                                        class="w-3 h-3"></i>
                                                </span>
                                            @else
                                                <span class="text-gray-300 text-xs italic">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-xs text-gray-400">No project
                                            history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">{{ $projects->links() }}</div>
                </div>

                {{-- Payout History Table --}}
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden mt-6">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Payout History</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Token</th>
                                    <th class="px-6 py-4">Amount (IDR)</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($payouts as $pay)
                                    <tr>
                                        <td class="px-6 py-4 text-gray-500">{{ $pay->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-medium">{{ number_format($pay->amount_token) }} TX</td>
                                        <td class="px-6 py-4 font-bold text-gray-900">Rp
                                            {{ number_format($pay->amount_currency, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase {{ $pay->status == 'approved' ? 'bg-green-100 text-green-700' : ($pay->status == 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ $pay->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">No payout
                                            history yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- INCLUDE MODAL PAYOUT (FILE TERPISAH) --}}
        @include('admin.performance.payout')

    </div>
@endsection
