{{-- PAYOUT CONFIRMATION MODAL --}}
{{-- FIX: Hapus class 'fade-in', ganti dengan x-transition Alpine JS --}}
<div x-show="payoutModalOpen" style="display: none;" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">

    {{-- Modal Content Wrapper --}}
    <div @click.away="payoutModalOpen = false" x-show="payoutModalOpen"
        x-transition:enter="transition ease-out duration-300 delay-75"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative">

        {{-- Modal Header --}}
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                <i data-feather="arrow-up-right" class="w-5 h-5"></i> Request Payout
            </h3>
            <button @click="payoutModalOpen = false" type="button"
                class="text-gray-400 hover:text-black hover:bg-gray-200 p-1 rounded-full transition-colors">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('staff.finance.payout') }}" method="POST">
            @csrf
            <div class="p-6 space-y-6">

                {{-- Info Bar: Balance & Rate --}}
                <div class="flex justify-between items-center bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Your Balance</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{-- Gunakan x-text agar reaktif --}}
                            <span x-text="maxBalance.toLocaleString()"></span> TX
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Current Rate</p>
                        <p class="text-sm font-bold text-blue-600">1 TX = Rp {{ number_format($rate, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Destination Details --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">Destination Account</p>
                        <a href="{{ route('staff.settings') }}"
                            class="text-[10px] font-bold text-blue-600 underline">Change</a>
                    </div>

                    @if ($user->bank_name && $user->bank_account)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-white rounded-lg text-blue-600 shadow-sm border border-blue-50">
                                <i data-feather="credit-card" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $user->bank_name }}</p>
                                <p class="text-sm font-mono text-gray-600 tracking-tight">{{ $user->bank_account }}</p>
                                <p class="text-xs text-gray-500 uppercase mt-0.5 font-bold">{{ $user->bank_holder }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div
                            class="flex items-center gap-2 text-red-600 bg-red-50 p-3 rounded-xl border border-red-100">
                            <i data-feather="alert-circle" class="w-4 h-4"></i>
                            <span class="text-xs font-bold">Bank details not set. Please update settings.</span>
                        </div>
                    @endif
                </div>

                {{-- Input Token --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs font-bold text-gray-500 uppercase">Amount to withdraw</label>
                        {{-- Set max balance on click using Alpine variable --}}
                        <span class="text-xs text-gray-400 font-bold cursor-pointer hover:text-black"
                            @click="tokenInput = maxBalance">
                            Max: <span x-text="maxBalance"></span>
                        </span>
                    </div>
                    <div class="relative">
                        <input type="number" name="token_amount" x-model.number="tokenInput" min="1"
                            :max="maxBalance"
                            class="w-full text-3xl font-bold p-4 pr-16 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-black focus:border-transparent transition-all placeholder-gray-300 outline-none"
                            placeholder="0">
                        <span
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">Tokens</span>
                    </div>
                    {{-- Error Message (Alpine logic) --}}
                    <p x-show="tokenInput > maxBalance" style="display: none;"
                        class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1">
                        <i data-feather="x-circle" class="w-3 h-3"></i> Insufficient balance
                    </p>
                </div>

                {{-- Conversion Result (Calculator) --}}
                <div class="p-5 bg-black rounded-2xl text-white shadow-lg relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-20 h-20 bg-gray-800 rounded-full blur-xl -mr-5 -mt-5 opacity-50">
                    </div>
                    <div class="relative z-10 flex justify-between items-end">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Estimated
                                (IDR)</p>
                            <p class="text-2xl font-bold font-mono text-white">
                                Rp <span x-text="(tokenInput * rate).toLocaleString('id-ID')">0</span>
                            </p>
                        </div>
                        <i data-feather="check-circle" class="w-8 h-8 text-green-500 opacity-50"></i>
                    </div>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="p-6 pt-0 bg-white">
                <button type="submit" :disabled="tokenInput <= 0 || tokenInput > maxBalance || !hasBank"
                    class="w-full py-4 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition-all shadow-lg active:translate-y-0.5 flex items-center justify-center gap-2">
                    <span>Confirm Payout Request</span>
                    <i data-feather="arrow-right" class="w-4 h-4"></i>
                </button>
                <p class="text-center text-[10px] text-gray-400 mt-3 font-medium">
                    Funds will be transferred within 24-48 hours after approval.
                </p>
            </div>
        </form>
    </div>
</div>
