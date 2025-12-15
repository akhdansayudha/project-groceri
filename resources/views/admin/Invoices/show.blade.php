@extends('admin.layouts.app')

@section('content')
    {{-- SWEETALERT CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-3xl mx-auto fade-in">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.invoices.index') }}"
                    class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    <i data-feather="arrow-left" class="w-5 h-5 text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Invoice Detail</h1>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $invoice->id }}</p>
                </div>
            </div>

            {{-- Manual Action Button (New Style) --}}
            @if ($invoice->status == 'unpaid')
                <button type="button" onclick="confirmPayment()"
                    class="px-6 py-3 bg-black text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition-all shadow-lg shadow-black/20 flex items-center gap-2 transform active:scale-95">
                    <i data-feather="check-circle" class="w-4 h-4"></i>
                    <span>Mark as Paid</span>
                </button>

                <form id="mark-paid-form" action="{{ route('admin.invoices.paid', $invoice->id) }}" method="POST"
                    class="hidden">
                    @csrf @method('PUT')
                </form>
            @endif
        </div>

        {{-- INVOICE PAPER --}}
        <div
            class="bg-white p-8 md:p-12 rounded-3xl border border-gray-200 shadow-xl shadow-gray-100 relative overflow-hidden">

            {{-- Watermark --}}
            @if ($invoice->status == 'paid')
                <div
                    class="absolute right-10 top-10 border-4 border-green-200 text-green-200 text-6xl font-black uppercase -rotate-12 px-4 py-2 opacity-50 select-none pointer-events-none">
                    PAID
                </div>
            @endif

            {{-- Header --}}
            <div class="flex justify-between items-start mb-12 border-b border-gray-100 pb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 bg-black rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                            V</div>
                        <span class="font-bold text-xl tracking-tight text-gray-900">VEKTORA</span>
                    </div>
                    <div class="text-xs text-gray-500 leading-relaxed">
                        <p class="font-bold text-gray-900">Creative Digital Agency</p>
                        <p>Surabaya, Indonesia</p>
                        <p>support@vektora.id</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-3xl font-mono font-bold text-gray-900 tracking-tight">{{ $invoice->invoice_number }}
                    </h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-500">Issued Date: <span
                                class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</span>
                        </p>
                        @if ($invoice->status == 'unpaid')
                            <p class="text-xs text-red-500 font-bold bg-red-50 inline-block px-2 py-0.5 rounded">Due Date:
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bill To --}}
            <div class="mb-12 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                <p class="text-[10px] uppercase font-bold text-gray-400 mb-2 tracking-wider">Billed To</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white border border-gray-200 overflow-hidden">
                        <img src="{{ $invoice->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($invoice->user->full_name) }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $invoice->user->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $invoice->user->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Item Table --}}
            <div class="mb-12">
                <table class="w-full text-left">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="py-3 text-xs uppercase font-bold text-black tracking-wide">Description</th>
                            <th class="py-3 text-xs uppercase font-bold text-black text-right tracking-wide">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-6 text-sm font-medium text-gray-700">
                                {{ $invoice->description }}
                            </td>
                            <td class="py-6 text-lg font-bold text-gray-900 text-right font-mono">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Total Calculation --}}
            <div class="flex justify-end mb-12">
                <div class="w-full md:w-1/2 bg-gray-50 rounded-2xl p-6">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-500 font-medium">Subtotal</span>
                        <span class="text-sm font-bold text-gray-900">Rp
                            {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-lg font-bold text-black">Total Due</span>
                        <span class="text-2xl font-black text-black">Rp
                            {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="text-center text-xs text-gray-400 leading-relaxed border-t border-gray-100 pt-8">
                <p class="font-bold text-gray-900 mb-1">Payment Instructions</p>
                @if ($invoice->status == 'paid')
                    <p class="text-green-600 font-bold">
                        <i data-feather="check" class="w-3 h-3 inline"></i> Payment successfully received on
                        {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d F Y, H:i') }}.
                    </p>
                @else
                    <p>Please make the payment before the due date via Bank Transfer or QRIS.</p>
                    <p>Reference ID: <span class="font-mono">{{ $invoice->id }}</span></p>
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPT: CUSTOM SWEETALERT --}}
    <script>
        // Mixin untuk Style Konsisten
        const BentoSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-3xl border border-gray-100 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-xl font-bold text-gray-900 mt-8 mb-2',
                htmlContainer: 'text-sm text-gray-500 mb-8 px-8',
                confirmButton: 'px-6 py-3 rounded-xl bg-black text-white font-bold text-sm shadow-lg shadow-black/20 hover:bg-gray-800 transition-all mx-2',
                cancelButton: 'px-6 py-3 rounded-xl bg-gray-50 text-gray-700 font-bold text-sm hover:bg-gray-100 border border-gray-200 transition-all mx-2',
                actions: 'mb-8 w-full flex justify-center gap-2'
            },
            buttonsStyling: false,
            reverseButtons: true,
            showClass: {
                popup: 'animate__animated animate__fadeInUp animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutDown animate__faster'
            }
        });

        function confirmPayment() {
            BentoSwal.fire({
                title: 'Confirm Payment?',
                html: "Anda akan menandai invoice ini sebagai <b>LUNAS</b>.<br>Sistem akan otomatis menambahkan token ke wallet user.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm Paid',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('mark-paid-form').submit();
                }
            })
        }

        @if (session('success'))
            BentoSwal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            BentoSwal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
            });
        @endif
    </script>
@endsection
