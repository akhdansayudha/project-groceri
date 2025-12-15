@extends('admin.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Invoices & Finance</h1>
            <p class="text-gray-500 text-sm">Pantau pendapatan dan status pembayaran klien.</p>
        </div>
    </div>

    {{-- STATS GRID (BENTO STYLE) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 fade-in">

        {{-- Card 1: Total Revenue (Hero) --}}
        <div class="bg-black text-white p-6 rounded-3xl relative overflow-hidden group shadow-xl shadow-black/10">
            <div
                class="absolute right-0 top-0 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-20 -mr-10 -mt-10 transition-all group-hover:scale-110">
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/10 rounded-xl backdrop-blur-sm">
                        <i data-feather="dollar-sign" class="w-5 h-5 text-white"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Revenue</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-500 mt-1">Accumulated from paid invoices</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Unpaid Amount --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-50 text-red-600 rounded-xl">
                        <i data-feather="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Outstanding Amount</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">Pending payments from clients</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Unpaid Count --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-yellow-50 text-yellow-600 rounded-xl">
                        <i data-feather="clock" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Invoices</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $countUnpaid }} <span
                            class="text-sm font-medium text-gray-400">Bills</span></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Waiting for payment confirmation</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS & TABLE --}}
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden fade-in flex flex-col">
        {{-- Toolbar --}}
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-white">
            <div class="flex gap-2 p-1 bg-gray-50 rounded-xl border border-gray-100">
                <a href="{{ route('admin.invoices.index') }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-white text-black shadow-sm' : 'text-gray-500 hover:text-black' }}">
                    All
                </a>
                <a href="{{ route('admin.invoices.index', ['status' => 'paid']) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'paid' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-green-600' }}">
                    Paid
                </a>
                <a href="{{ route('admin.invoices.index', ['status' => 'unpaid']) }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ request('status') == 'unpaid' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-red-600' }}">
                    Unpaid
                </a>
            </div>

            <form action="{{ route('admin.invoices.index') }}" method="GET" class="relative group w-full md:w-72">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search invoice number or client..."
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
                        <th class="px-6 py-4 tracking-wider">Invoice #</th>
                        <th class="px-6 py-4 tracking-wider">Client</th>
                        <th class="px-6 py-4 tracking-wider">Amount</th>
                        <th class="px-6 py-4 tracking-wider">Issued Date</th>
                        <th class="px-6 py-4 tracking-wider">Status</th>
                        <th class="px-6 py-4 tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50/80 transition-colors group cursor-pointer"
                            onclick="window.location='{{ route('admin.invoices.show', $inv->id) }}'">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900 group-hover:text-black">
                                {{ $inv->invoice_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                                        <img src="{{ $inv->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($inv->user->full_name) . '&background=random&color=fff' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">
                                            {{ Str::limit($inv->user->full_name, 20) }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $inv->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">Rp
                                    {{ number_format($inv->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-500">
                                {{ \Carbon\Carbon::parse($inv->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($inv->status == 'paid')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-green-50 text-green-700 border border-green-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div> Paid
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></div> Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.invoices.show', $inv->id) }}"
                                    class="p-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-lg transition-all inline-block">
                                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="p-3 bg-gray-50 rounded-full">
                                        <i data-feather="file-text" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Tidak ada invoice ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
@endsection
