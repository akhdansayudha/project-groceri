@extends('client.layouts.app')

@section('content')
    <div class="mb-8 fade-in">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('client.wallet.index') }}"
                class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-3xl font-bold tracking-tight">Top Up Token</h1>
        </div>
        <p class="text-gray-500 ml-14">Enter the amount of tokens you need. Buy more to get better rates.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in">

        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                <form action="{{ route('client.wallet.topup.process') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">How many
                            tokens?</label>
                        <div class="relative">
                            <input type="number" id="token_input" name="token_amount" min="1" value="10"
                                required
                                class="w-full text-5xl font-bold border-b-2 border-gray-200 py-4 focus:border-black outline-none bg-transparent transition-colors"
                                oninput="calculatePrice()">
                            <span class="absolute right-0 bottom-6 text-xl font-bold text-gray-400">TX</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Minimum purchase: 1 TX</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Rate applied</span>
                            <span class="font-bold" id="rate_display">Rp 0 / token</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                            <span class="text-lg font-bold">Total Payment</span>
                            <span class="text-3xl font-bold text-blue-600" id="total_display">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-black text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition-all shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                        <span>Create Invoice</span>
                        <i data-feather="arrow-right" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-[#111] text-white p-6 rounded-3xl shadow-xl sticky top-24">
                <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                    <i data-feather="tag" class="w-4 h-4 text-yellow-400"></i> Price List
                </h3>

                <div class="space-y-3">
                    @foreach ($prices as $price)
                        <div class="flex justify-between items-center text-sm border-b border-gray-800 pb-2 last:border-0">
                            <div>
                                <span class="block font-bold text-yellow-400">{{ $price->min_qty }} -
                                    {{ $price->max_qty >= 99999 ? '∞' : $price->max_qty }} TX</span>
                                <span class="text-xs text-gray-400">{{ $price->label }}</span>
                            </div>
                            <div class="font-bold">
                                Rp {{ number_format($price->price_per_token, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t border-gray-800 text-xs text-gray-400 leading-relaxed">
                    *Harga akan otomatis menyesuaikan berdasarkan jumlah token yang Anda input di form sebelah kiri.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ambil data harga dari Controller dan ubah jadi JSON
        const priceTiers = @json($prices);

        function calculatePrice() {
            let amount = parseInt(document.getElementById('token_input').value) || 0;
            let pricePerToken = 0;
            let found = false;

            // Loop logic harga
            for (let i = 0; i < priceTiers.length; i++) {
                let tier = priceTiers[i];
                // Cek apakah amount masuk range ini
                // Gunakan 999999999 untuk handle unlimited max_qty
                let max = tier.max_qty >= 99999 ? 999999999 : tier.max_qty;

                if (amount >= tier.min_qty && amount <= max) {
                    pricePerToken = tier.price_per_token;
                    found = true;
                    break;
                }
            }

            // Fallback jika jumlah sangat besar (pakai tier terakhir/termurah)
            if (!found && priceTiers.length > 0) {
                pricePerToken = priceTiers[priceTiers.length - 1].price_per_token;
            }

            let total = amount * pricePerToken;

            // Update UI
            document.getElementById('rate_display').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(
                pricePerToken) + " / token";
            document.getElementById('total_display').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(total);
        }

        // Jalankan sekali saat load
        calculatePrice();
    </script>
@endsection
