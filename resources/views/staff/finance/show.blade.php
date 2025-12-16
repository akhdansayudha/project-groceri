@extends('staff.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto fade-in pb-20">

        {{-- BACK BUTTON --}}
        <div class="mb-6">
            <a href="{{ route('staff.finance.earnings') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-black transition-colors font-medium">
                <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Earnings
            </a>
        </div>

        {{-- INVOICE CARD --}}
        <div class="bg-white border border-gray-200 rounded-3xl shadow-lg overflow-hidden relative">

            {{-- Decorative Status Bar --}}
            <div
                class="h-2 w-full 
                {{ $payout->status == 'approved' ? 'bg-green-500' : ($payout->status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
            </div>

            <div class="p-8">

                {{-- Header: ID & Status --}}
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Payout Request</p>
                        <h1 class="text-3xl font-bold text-gray-900 font-mono">
                            #PY-{{$payout->id}}</h1>
                    </div>

                    @if ($payout->status == 'pending')
                        <div
                            class="px-4 py-2 rounded-xl bg-yellow-50 text-yellow-700 border border-yellow-100 flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wide">Processing</span>
                        </div>
                    @elseif($payout->status == 'approved')
                        <div
                            class="px-4 py-2 rounded-xl bg-green-50 text-green-700 border border-green-100 flex items-center gap-2">
                            <div class="bg-green-500 rounded-full p-0.5"><i data-feather="check"
                                    class="w-3 h-3 text-white"></i></div>
                            <span class="text-xs font-bold uppercase tracking-wide">Request Approved</span>
                        </div>
                    @else
                        <div
                            class="px-4 py-2 rounded-xl bg-red-50 text-red-700 border border-red-100 flex items-center gap-2">
                            <i data-feather="x-circle" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase tracking-wide">Rejected</span>
                        </div>
                    @endif
                </div>

                {{-- Amount Section --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-center border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Amount</p>
                    <h2 class="text-4xl font-bold text-gray-900 mb-2">
                        Rp {{ number_format($payout->amount_currency, 0, ',', '.') }}
                    </h2>
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-gray-200 shadow-sm">
                        <i data-feather="layers" class="w-3 h-3 text-gray-400"></i>
                        <span class="text-xs font-bold text-gray-600">{{ number_format($payout->amount_token) }} Tokens
                            Deducted</span>
                    </div>
                </div>

                {{-- ADMIN NOTE SECTION --}}
                {{-- Ini akan menampilkan catatan admin jika ada --}}
                @if ($payout->admin_note)
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8 relative overflow-hidden">
                        <div class="flex gap-4">
                            <div class="p-2 bg-blue-100 rounded-lg h-fit text-blue-600">
                                <i data-feather="message-square" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wide mb-1">Message from Admin
                                </h4>
                                <p class="text-sm text-blue-900 leading-relaxed font-medium">{{ $payout->admin_note }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    {{-- Left: Bank Info --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2 mb-3">
                            Transfer Destination
                        </h4>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-900">
                                {{ $payout->bank_name ?? $user->bank_name }}
                            </p>
                            <p class="text-sm font-mono text-gray-600">
                                {{ $payout->bank_account ?? $user->bank_account }}
                            </p>
                            <p class="text-sm text-gray-500 uppercase">
                                {{ $payout->bank_holder ?? $user->bank_holder }}
                            </p>
                        </div>
                    </div>

                    {{-- Right: Timeline --}}
                    <div>
                        <h4
                            class="text-xs font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2 mb-3">
                            Timeline
                        </h4>
                        <div class="relative pl-4 border-l-2 border-gray-100 space-y-4">
                            {{-- Created --}}
                            <div class="relative">
                                <div
                                    class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-gray-300 border-2 border-white">
                                </div>
                                <p class="text-xs text-gray-500">Request Created</p>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($payout->created_at)->format('d F Y, H:i') }}</p>
                            </div>

                            {{-- Updated / Processed --}}
                            @if ($payout->status != 'pending')
                                <div class="relative">
                                    <div
                                        class="absolute -left-[21px] top-1 w-3 h-3 rounded-full {{ $payout->status == 'approved' ? 'bg-green-500' : 'bg-red-500' }} border-2 border-white">
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ $payout->status == 'approved' ? 'Request Approved' : 'Request Rejected' }}
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($payout->updated_at)->format('d F Y, H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- PROOF SECTION --}}
                @if ($payout->status == 'approved' && $payout->proof_url)
                    {{-- Konstruksi URL manual sesuai request: ENV + path --}}
                    @php
                        $proofFullUrl = rtrim(env('SUPABASE_URL'), '/') . '/' . $payout->proof_url;
                    @endphp

                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <i data-feather="image" class="w-4 h-4"></i> Transfer Proof
                        </h4>
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-2 shadow-sm">
                            <a href="{{ $proofFullUrl }}" target="_blank" class="block relative group">
                                {{-- Tampilkan gambar langsung, full opacity --}}
                                <img src="{{ $proofFullUrl }}" alt="Transfer Proof"
                                    class="w-full h-auto object-contain rounded-lg bg-gray-100 border border-gray-100 max-h-[500px]">

                                {{-- Overlay tombol zoom saat hover --}}
                                <div
                                    class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <span class="px-4 py-2 bg-white rounded-full text-xs font-bold text-black shadow-lg">
                                        Click to view full size
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            {{-- FOOTER HELP --}}
            <div
                class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span>Vektora Finance Team</span>
                <a href="#" class="hover:text-black underline">Need Help?</a>
            </div>
        </div>
    </div>
@endsection
