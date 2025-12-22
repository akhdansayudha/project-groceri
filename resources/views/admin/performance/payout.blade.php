{{-- MANUAL PAYOUT MODAL (ADMIN) --}}
<div x-show="payoutModalOpen" style="display: none;" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">

    {{-- Modal Content --}}
    <div @click.away="payoutModalOpen = false" x-show="payoutModalOpen"
        x-transition:enter="transition ease-out duration-300 delay-75"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                    <i data-feather="dollar-sign" class="w-5 h-5 text-black"></i> Process Payout
                </h3>
                <p class="text-xs text-gray-500 mt-1">Transfer manual ke rekening staff.</p>
            </div>
            <button @click="payoutModalOpen = false" type="button"
                class="text-gray-400 hover:text-black p-2 hover:bg-gray-200 rounded-full transition-colors">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.performance.manual_payout', $staff->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="p-6 space-y-6">
                {{-- Bank Details Staff --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Destination Account
                    </p>
                    @if ($staff->bank_name)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 shadow-sm">
                                <i data-feather="credit-card" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $staff->bank_name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $staff->bank_account }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $staff->bank_holder }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-red-500 text-xs flex items-center gap-2 font-bold">
                            <i data-feather="alert-circle" class="w-4 h-4"></i> Bank details not set.
                        </div>
                    @endif
                </div>

                {{-- Input Amount --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs font-bold text-gray-500 uppercase">Amount to Transfer</label>
                        <span class="text-xs text-gray-400 font-bold cursor-pointer hover:text-black transition-colors"
                            @click="tokenInput = maxBalance">
                            Max: <span x-text="maxBalance"></span> TX
                        </span>
                    </div>
                    <div class="relative">
                        <input type="number" name="amount_token" x-model.number="tokenInput" min="1"
                            :max="maxBalance" required
                            class="w-full text-3xl font-bold p-4 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-black outline-none transition-all placeholder-gray-300"
                            placeholder="0">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Tokens</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Current Rate: 1 TX = Rp
                        {{ number_format($currentRate, 0, ',', '.') }}</p>
                </div>

                {{-- Calculator Result --}}
                <div class="p-5 bg-black rounded-2xl text-white shadow-lg relative overflow-hidden group">
                    <div
                        class="absolute right-0 top-0 w-24 h-24 bg-gray-800 rounded-full blur-2xl -mr-5 -mt-5 opacity-50 group-hover:opacity-70 transition-opacity">
                    </div>
                    <div class="relative z-10 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Transfer
                                (IDR)</p>
                            <p class="text-2xl font-bold font-mono">
                                Rp <span x-text="(tokenInput * rate).toLocaleString('id-ID')">0</span>
                            </p>
                        </div>
                        <i data-feather="arrow-right-circle"
                            class="w-8 h-8 text-white opacity-20 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                </div>

                {{-- Upload Proof --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Transfer Proof
                        (Optional)</label>
                    <input type="file" name="proof_file" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-all cursor-pointer border border-gray-200 rounded-xl">
                </div>

                {{-- Admin Note --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Note</label>
                    <input type="text" name="admin_note" placeholder="e.g. Pembayaran Gaji Bulan Ini"
                        class="w-full p-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-black transition-colors">
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 pt-0">
                <button type="submit" :disabled="tokenInput <= 0 || tokenInput > maxBalance"
                    class="w-full py-4 bg-black text-white rounded-xl font-bold text-sm hover:scale-[1.02] active:scale-[0.98] disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed disabled:scale-100 disabled:shadow-none transition-all shadow-lg flex items-center justify-center gap-2">
                    <span>Confirm & Deduct Balance</span>
                    <i data-feather="check" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>
