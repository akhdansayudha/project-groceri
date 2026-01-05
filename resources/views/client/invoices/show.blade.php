@extends('client.layouts.app')

@section('content')
    {{-- Midtrans Snap JS (Dynamic Environment) --}}
    @if (config('services.midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}">
        </script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    <div class="max-w-4xl mx-auto fade-in pb-10">

        {{-- Navigation --}}
        <div class="mb-8">
            <a href="{{ route('client.invoices.index') }}"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-black transition-colors text-sm font-bold group">
                <div class="p-2 bg-white rounded-full border border-gray-200 group-hover:border-black transition-colors">
                    <i data-feather="arrow-left" class="w-4 h-4"></i>
                </div>
                <span>Back to Invoices</span>
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="bg-green-50 text-green-700 p-4 rounded-2xl mb-6 border border-green-200 flex items-center gap-3 shadow-sm">
                <div class="bg-green-100 p-2 rounded-full"><i data-feather="check" class="w-4 h-4"></i></div>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-50 text-red-700 p-4 rounded-2xl mb-6 border border-red-200 flex items-center gap-3 shadow-sm">
                <div class="bg-red-100 p-2 rounded-full"><i data-feather="alert-circle" class="w-4 h-4"></i></div>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- MAIN INVOICE CARD --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden relative">

            {{-- Watermark Status --}}
            @if ($invoice->status == 'paid')
                <div class="absolute top-0 right-0 p-10 opacity-10 pointer-events-none">
                    <div
                        class="border-4 border-green-600 text-green-600 font-black text-8xl uppercase transform -rotate-12 px-10 py-4 rounded-xl">
                        PAID</div>
                </div>
            @elseif ($invoice->status == 'cancelled')
                <div class="absolute top-0 right-0 p-10 opacity-10 pointer-events-none">
                    <div
                        class="border-4 border-gray-400 text-gray-500 font-black text-6xl uppercase transform -rotate-12 px-10 py-4 rounded-xl">
                        CANCELLED</div>
                </div>
            @endif

            {{-- HEADER SECTION --}}
            <div class="bg-gray-50/50 p-8 md:p-12 border-b border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start gap-8">

                    {{-- Logo & Address --}}
                    <div>
                        <div class="text-3xl font-bold tracking-tighter mb-6">
                            vektora<span class="text-blue-600">.</span>
                        </div>
                        <div class="text-sm text-gray-500 space-y-1">
                            <p class="font-bold text-black">Vektora Creative Agency</p>
                            <p>Surabaya, Indonesia</p>
                            <p>support@vektora.agency</p>
                        </div>
                    </div>

                    {{-- Invoice Meta --}}
                    <div class="text-left md:text-right">
                        <h1 class="text-lg font-bold uppercase tracking-widest text-gray-400 mb-2">Invoice</h1>
                        <p class="text-3xl font-bold text-gray-900 tracking-tight mb-2">#{{ $invoice->invoice_number }}</p>

                        {{-- Status Badge --}}
                        @php
                            $statusColors = match ($invoice->status) {
                                'paid' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-gray-200 text-gray-600',
                                'unpaid', 'pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-600',
                            };
                            $dotColor = match ($invoice->status) {
                                'paid' => 'bg-green-600',
                                'cancelled' => 'bg-gray-500',
                                default => 'bg-yellow-600',
                            };
                        @endphp
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusColors }}">
                            <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                            {{ $invoice->status }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- BODY SECTION --}}
            <div class="p-8 md:p-12">

                {{-- Client & Dates Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Billed To</p>
                        <p class="font-bold text-lg text-gray-900">{{ $invoice->user->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $invoice->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Issued Date</p>
                        <p class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-feather="calendar" class="w-4 h-4 text-gray-400"></i>
                            {{ $invoice->created_at->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $invoice->created_at->format('H:i') }} WIB</p>
                    </div>
                    <div>
                        {{-- LOGIC TAMPILAN TANGGAL BERDASARKAN STATUS --}}
                        @if ($invoice->status == 'paid')
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Payment Date</p>
                            <p class="font-bold text-green-600 flex items-center gap-2">
                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($invoice->paid_at)->format('H:i') }} WIB</p>
                        @elseif($invoice->status == 'cancelled')
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Cancelled Date</p>
                            <p class="font-bold text-gray-500 flex items-center gap-2">
                                <i data-feather="x-circle" class="w-4 h-4"></i>
                                {{ $invoice->updated_at->format('d M Y') }}
                            </p>
                        @else
                            {{-- TAMPILAN DUE DATE / COUNTDOWN --}}
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest mb-2">Pay Before</p>
                            @if ($invoice->due_date)
                                <p class="font-bold text-red-600 flex items-center gap-2">
                                    <i data-feather="clock" class="w-4 h-4"></i>
                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('H:i') }} WIB
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                </p>

                                {{-- Simple Countdown Script (Optional) --}}
                                <div id="countdown" class="text-[10px] font-bold text-red-500 mt-1 animate-pulse"></div>
                            @else
                                <p class="font-bold text-gray-900">-</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Item Details Table --}}
                <div class="border border-gray-100 rounded-2xl overflow-hidden mb-10">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Price</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-6 py-5">
                                    <p class="font-bold text-gray-900 text-base">{{ $details['item_name'] }}</p>
                                    <p class="text-gray-500 text-xs mt-1">{{ $invoice->description }}</p>
                                </td>
                                <td class="px-6 py-5 text-center font-medium">{{ $details['qty'] }}</td>
                                <td class="px-6 py-5 text-right font-medium text-gray-600">
                                    Rp {{ number_format($details['price_per_unit'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-gray-900">
                                    Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3"
                                    class="px-6 py-6 text-right font-bold text-gray-500 uppercase tracking-widest text-xs">
                                    Total Amount</td>
                                <td class="px-6 py-6 text-right">
                                    <span class="text-2xl font-bold text-black tracking-tight">
                                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 pt-8">
                    <div class="text-xs text-gray-400 max-w-sm">
                        *This invoice is automatically generated by Vektora System.
                        If you have any questions, please contact support.
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-6 w-full md:w-auto justify-end">

                        {{-- TOMBOL BAYAR & CANCEL (JIKA BELUM LUNAS) --}}
                        @if (in_array($invoice->status, ['unpaid', 'pending']))
                            {{-- Cancel Button (Red Text) --}}
                            <button type="button" onclick="openCancelModal()"
                                class="text-red-500 hover:text-red-700 font-bold text-sm transition-colors hover-target text-center">
                                Cancel Invoice
                            </button>

                            {{-- Pay Button --}}
                            <button id="pay-button"
                                class="px-8 py-4 bg-black text-white rounded-xl font-bold text-sm hover:scale-105 transition-transform shadow-xl shadow-black/20 flex items-center justify-center gap-2 w-full md:w-auto">
                                <span>Pay Now</span>
                                <i data-feather="arrow-right" class="w-4 h-4"></i>
                            </button>
                        @endif

                        {{-- TOMBOL DOWNLOAD PDF (JIKA SUDAH LUNAS) --}}
                        @if ($invoice->status == 'paid')
                            <a href="{{ route('client.invoices.pdf', $invoice->id) }}"
                                class="px-6 py-3 border-2 border-gray-100 hover:border-black text-gray-600 hover:text-black bg-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 group">
                                <i data-feather="download" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                <span>Download Receipt</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI CANCEL (Vektora Style) --}}
    <div id="cancelModal" class="fixed inset-0 z-50 hidden fade-in">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeCancelModal()"></div>

        {{-- Modal Content --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-4">
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl transform scale-100 transition-all text-center">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500">
                    <i data-feather="alert-triangle" class="w-8 h-8"></i>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-2 tracking-tight">Cancel Invoice?</h3>
                <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                    Are you sure you want to cancel invoice <b>#{{ $invoice->invoice_number }}</b>? This action cannot be
                    undone.
                </p>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="closeCancelModal()"
                        class="px-6 py-3.5 border border-gray-200 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors">
                        No, Keep it
                    </button>

                    <form action="{{ route('client.invoices.cancel', $invoice->id) }}" method="POST" class="w-full">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full px-6 py-3.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                            Yes, Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Modal Logic
        const modal = document.getElementById('cancelModal');

        function openCancelModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Countdown Logic (Simple)
        @if (in_array($invoice->status, ['unpaid', 'pending']) && $invoice->due_date)
            const dueDate = new Date("{{ $invoice->due_date }}").getTime();
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = dueDate - now;

                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "EXPIRED - Refreshing page...";
                    setTimeout(() => window.location.reload(), 2000);
                    return;
                }

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("countdown").innerHTML = minutes + "m " + seconds + "s remaining";
            }, 1000);
        @endif

        // Payment Logic
        @if (in_array($invoice->status, ['unpaid', 'pending']) && $invoice->snap_token)
            var payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function() {
                    window.snap.pay('{{ $invoice->snap_token }}', {
                        onSuccess: function(result) {
                            window.location.reload();
                        },
                        onPending: function(result) {
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                            window.location.reload();
                        },
                        onClose: function() {}
                    });
                });
            }
        @endif
    </script>
@endsection
