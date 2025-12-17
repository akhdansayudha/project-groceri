@extends('client.layouts.app')

@section('content')
    {{-- Midtrans Snap JS --}}
    {{-- Pastikan CLIENT KEY sudah ada di .env Anda --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="max-w-3xl mx-auto fade-in">

        <div class="mb-6">
            <a href="{{ route('client.invoices.index') }}"
                class="flex items-center gap-2 text-gray-500 hover:text-black transition-colors text-sm font-bold">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Invoices
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 border border-green-100 flex items-center gap-3">
                <i data-feather="check-circle" class="w-5 h-5"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 flex items-center gap-3">
                <i data-feather="alert-circle" class="w-5 h-5"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 md:p-12 rounded-3xl border border-gray-200 shadow-xl relative overflow-hidden">

            @if ($invoice->status == 'paid')
                <div
                    class="absolute top-10 right-10 border-4 border-green-200 text-green-200 font-black text-6xl uppercase transform -rotate-12 opacity-50 select-none pointer-events-none">
                    PAID
                </div>
            @endif

            <div class="flex justify-between items-start border-b border-gray-100 pb-8 mb-8">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Invoice</h1>
                    <p class="text-gray-500 text-sm mt-1">#{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Amount Due</p>
                    <h2 class="text-3xl font-bold text-black mt-1">Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Billed To</p>
                    <p class="font-bold">{{ $invoice->user->full_name ?? explode('@', $invoice->user->email)[0] }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->user->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Due Date</p>
                    <p class="font-bold">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</p>

                    <div class="mt-4">
                        @php
                            $statusColors = match ($invoice->status) {
                                'paid' => 'bg-green-100 text-green-700',
                                'unpaid' => 'bg-yellow-100 text-yellow-800',
                                'pending' => 'bg-orange-100 text-orange-800',
                                'failed' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span
                            class="inline-block px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider {{ $statusColors }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-8">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-200">
                            <th class="pb-2 font-medium">Description</th>
                            <th class="pb-2 font-medium text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="pt-2 font-bold">{{ $invoice->description }}</td>
                            <td class="pt-2 text-right font-bold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- LOGIC TOMBOL BAYAR --}}
            @if ($invoice->status == 'unpaid' || $invoice->status == 'pending')
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">Complete your payment securely via Midtrans</p>

                    <button id="pay-button"
                        class="w-full bg-black text-white py-4 rounded-xl font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                        <i data-feather="credit-card" class="w-5 h-5"></i>
                        <span>Pay Now</span>
                    </button>

                    <p class="text-xs text-gray-400 mt-4">
                        *Secure Payment Gateway by Midtrans
                    </p>
                </div>
            @else
                {{-- JIKA SUDAH LUNAS --}}
                <div class="text-center border-t border-gray-100 pt-8">
                    <p class="text-sm text-gray-500 mb-2">Payment completed on
                        {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y, H:i') : '-' }}</p>
                    <button
                        class="text-black font-bold text-sm border border-gray-200 px-6 py-2 rounded-lg hover:bg-gray-50 flex items-center gap-2 mx-auto">
                        <i data-feather="download" class="w-4 h-4"></i> Download Receipt (PDF)
                    </button>
                </div>
            @endif

        </div>
    </div>

    {{-- SCRIPT HANDLER PAYMENT --}}
    @if (($invoice->status == 'unpaid' || $invoice->status == 'pending') && $invoice->snap_token)
        <script type="text/javascript">
            var payButton = document.getElementById('pay-button');
            payButton.addEventListener('click', function() {
                // Trigger snap popup
                window.snap.pay('{{ $invoice->snap_token }}', {
                    onSuccess: function(result) {
                        /* Payment Success */
                        window.location.reload();
                    },
                    onPending: function(result) {
                        /* Payment Pending */
                        window.location.reload();
                    },
                    onError: function(result) {
                        /* Payment Failed */
                        alert("Pembayaran gagal!");
                        window.location.reload();
                    },
                    onClose: function() {
                        /* User Closed Popup */
                        // Tidak melakukan apa-apa, biarkan user klik pay lagi jika mau
                    }
                });
            });
        </script>
    @endif
@endsection
