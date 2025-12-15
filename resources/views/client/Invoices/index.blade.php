@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-1">Invoices & Billing</h1>
            <p class="text-gray-500 text-sm">Kelola tagihan pembayaran dan riwayat transaksi Anda.</p>
        </div>

        <a href="{{ route('client.wallet.topup') }}"
            class="bg-black text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all flex items-center gap-2 shadow-lg shadow-black/20 group">
            <i data-feather="plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
            <span>Create New Bill</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 fade-in">
        {{-- Card Unpaid --}}
        <div
            class="bg-white p-6 rounded-3xl border border-red-100 shadow-sm relative overflow-hidden group hover:border-red-200 transition-colors">
            <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i data-feather="alert-circle" class="w-24 h-24 text-red-600 transform rotate-12"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Unpaid Balance</p>
                </div>
                <h3 class="text-3xl font-bold text-red-600 tracking-tight">
                    Rp {{ number_format($stats['unpaid_amount'], 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-400 mt-2 font-medium">{{ $stats['unpaid'] }} Tagihan Belum Dibayar</p>
            </div>
        </div>

        {{-- Card Paid --}}
        <div
            class="bg-white p-6 rounded-3xl border border-green-100 shadow-sm relative overflow-hidden group hover:border-green-200 transition-colors">
            <div class="absolute right-0 top-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i data-feather="check-circle" class="w-24 h-24 text-green-600 transform -rotate-12"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Lifetime Paid</p>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 tracking-tight">
                    Rp {{ number_format($stats['paid_total'], 0, ',', '.') }}
                </h3>
                <p class="text-xs text-green-600 mt-2 font-bold">Terverifikasi & Lunas</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm fade-in">

        {{-- HEADER & FILTER --}}
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 rounded-lg text-gray-600">
                    <i data-feather="file-text" class="w-5 h-5"></i>
                </div>
                <h3 class="font-bold text-lg">Billing History</h3>
            </div>

            {{-- Filter Date Form --}}
            <form action="{{ route('client.invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                    <i data-feather="calendar" class="w-4 h-4 text-gray-400 mr-2"></i>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="bg-transparent text-xs font-bold text-gray-600 outline-none w-28">
                    <span class="text-gray-400 mx-2">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="bg-transparent text-xs font-bold text-gray-600 outline-none w-28">
                </div>
                <button type="submit"
                    class="bg-black text-white p-2.5 rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10"
                    title="Apply Filter">
                    <i data-feather="search" class="w-4 h-4"></i>
                </button>
                @if (request('start_date') || request('end_date'))
                    <a href="{{ route('client.invoices.index') }}"
                        class="bg-gray-100 text-gray-500 p-2.5 rounded-xl hover:bg-gray-200 transition-colors"
                        title="Reset Filter">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- Invoice Number --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 block">{{ $inv->invoice_number }}</span>
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                {{ Str::limit($inv->description, 40) }}
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4">
                                <span class="text-gray-900 font-bold block">{{ $inv->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $inv->created_at->format('H:i') }}</span>
                            </td>

                            {{-- Amount --}}
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($inv->amount, 0, ',', '.') }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = match ($inv->status) {
                                        'paid' => [
                                            'bg' => 'bg-green-100',
                                            'text' => 'text-green-700',
                                            'icon' => 'check-circle',
                                        ],
                                        'unpaid' => [
                                            'bg' => 'bg-yellow-100',
                                            'text' => 'text-yellow-800',
                                            'icon' => 'clock',
                                        ],
                                        'failed' => [
                                            'bg' => 'bg-red-100',
                                            'text' => 'text-red-700',
                                            'icon' => 'x-circle',
                                        ],
                                        default => [
                                            'bg' => 'bg-gray-100',
                                            'text' => 'text-gray-600',
                                            'icon' => 'help-circle',
                                        ],
                                    };
                                @endphp
                                <div
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <i data-feather="{{ $statusConfig['icon'] }}" class="w-3 h-3"></i>
                                    {{ ucfirst($inv->status) }}
                                </div>
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- COPY BUTTON --}}
                                    @php
                                        $copyText =
                                            "Invoice: {$inv->invoice_number}\nDesc: {$inv->description}\nDate: {$inv->created_at->format(
        'd M Y',
    )}\nAmount: Rp " .
                                            number_format($inv->amount, 0, ',', '.') .
                                            "\nStatus: " .
                                            ucfirst($inv->status);
                                    @endphp
                                    <button onclick="copyToClipboard(`{{ $copyText }}`, '{{ $inv->invoice_number }}')"
                                        class="p-2 text-gray-400 hover:text-black hover:bg-gray-200 rounded-lg transition-colors"
                                        title="Copy Details">
                                        <i data-feather="copy" class="w-4 h-4"></i>
                                    </button>

                                    @if ($inv->status == 'unpaid')
                                        <a href="{{ route('client.invoices.show', $inv->id) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-black/10">
                                            Pay
                                        </a>
                                    @else
                                        <a href="{{ route('client.invoices.show', $inv->id) }}"
                                            class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-black transition-colors">
                                            View
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                        <i data-feather="file-minus" class="w-8 h-8"></i>
                                    </div>
                                    <p class="text-gray-900 font-bold text-lg">No invoices found</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your date filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    {{-- SCRIPT COPY TO CLIPBOARD + TOAST --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inisialisasi Toast SweetAlert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        function copyToClipboard(text, invoiceNumber) {
            navigator.clipboard.writeText(text).then(function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: `Data invoice ${invoiceNumber} berhasil disalin.`
                });
            }, function(err) {
                Toast.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyalin teks.'
                });
            });
        }
    </script>
@endsection
