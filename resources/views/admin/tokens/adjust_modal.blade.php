<div id="adjustModal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeAdjustModal()">
    </div>

    {{-- Modal Content --}}
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-3xl p-8 shadow-2xl transform transition-all scale-100">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Manual Adjustment</h3>
                <p class="text-sm text-gray-500">Tambah atau kurangi saldo user secara manual.</p>
            </div>
            <button onclick="closeAdjustModal()"
                class="p-2 bg-gray-50 hover:bg-gray-200 rounded-full text-gray-500 transition-colors">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.tokens.adjust') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Select User --}}
            <div class="group">
                <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block tracking-wider">Target
                    User</label>
                <div class="relative">
                    <select name="user_id" required
                        class="w-full bg-gray-50 border border-transparent hover:border-gray-300 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-black transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>Select a client...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <i data-feather="chevron-down"
                        class="w-4 h-4 text-gray-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
            </div>

            {{-- Action Type (CLEAN DESIGN) --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Option 1: Add Balance --}}
                <label class="cursor-pointer relative">
                    <input type="radio" name="type" value="credit" class="peer sr-only" checked>
                    <div
                        class="p-4 rounded-2xl bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all text-center 
                                peer-checked:bg-green-50 peer-checked:text-green-700 peer-checked:ring-1 peer-checked:ring-green-200">
                        <div class="mb-2">
                            <i data-feather="plus-circle"
                                class="w-6 h-6 mx-auto opacity-50 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-xs font-medium block peer-checked:font-bold">Add Balance</span>
                    </div>
                </label>

                {{-- Option 2: Deduct Balance --}}
                <label class="cursor-pointer relative">
                    <input type="radio" name="type" value="debit" class="peer sr-only">
                    <div
                        class="p-4 rounded-2xl bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all text-center 
                                peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:ring-1 peer-checked:ring-red-200">
                        <div class="mb-2">
                            <i data-feather="minus-circle"
                                class="w-6 h-6 mx-auto opacity-50 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-xs font-medium block peer-checked:font-bold">Deduct Balance</span>
                    </div>
                </label>
            </div>

            {{-- Amount & Reason --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-2 block tracking-wider">Amount
                        (Token)</label>
                    <input type="number" name="amount" placeholder="e.g. 50" min="1" required
                        class="w-full bg-gray-50 border border-transparent hover:border-gray-300 rounded-xl px-4 py-3 font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-black transition-all">
                </div>
                <div>
                    <label
                        class="text-[10px] uppercase font-bold text-gray-400 mb-2 block tracking-wider">Reason</label>
                    <input type="text" name="description" placeholder="e.g. Bonus, Refund" required
                        class="w-full bg-gray-50 border border-transparent hover:border-gray-300 rounded-xl px-4 py-3 font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-black transition-all">
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                    class="w-full py-4 rounded-xl bg-black text-white font-bold text-sm hover:bg-gray-800 shadow-xl shadow-black/20 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                    Confirm Adjustment
                </button>
            </div>
        </form>
    </div>
</div>
