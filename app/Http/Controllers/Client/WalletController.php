<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use Illuminate\Support\Str;
use App\Models\TokenPrice;
use App\Models\Tier;

class WalletController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil Data Wallet & Tier Saat Ini
        $wallet = $user->wallet()->with('tier')->firstOrFail();

        // 2. Ambil History Transaksi
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 3. Ambil Semua Tier untuk Kalkulasi Progress (Urutkan dari terendah)
        $tiers = Tier::orderBy('min_toratix', 'asc')->get();

        return view('client.wallet.index', compact('wallet', 'transactions', 'tiers'));
    }

    // Halaman Form Topup
    public function topup()
    {
        // Ambil daftar harga untuk ditampilkan di tabel referensi
        $prices = TokenPrice::orderBy('min_qty', 'asc')->get();
        return view('client.wallet.topup', compact('prices'));
    }

    // Proses Buat Invoice (Dynamic Calculation)
    public function processTopup(Request $request)
    {
        $request->validate([
            'token_amount' => 'required|integer|min:1'
        ]);

        $amount = $request->token_amount;

        // 1. Cari Harga per Token berdasarkan range
        $pricing = TokenPrice::where('min_qty', '<=', $amount)
            ->where('max_qty', '>=', $amount)
            ->first();

        // Fallback jika jumlah melebihi range tertinggi di DB (Pakai harga termurah/terakhir)
        if (!$pricing) {
            $pricing = TokenPrice::orderBy('max_qty', 'desc')->first();
        }

        $pricePerToken = $pricing->price_per_token;
        $totalBill = $amount * $pricePerToken;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Buat Invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'amount' => $totalBill, // Total Rupiah
            'status' => 'unpaid',
            // Simpan info jumlah token di deskripsi agar mudah diparsing saat pembayaran sukses
            'description' => "Top Up {$amount} Toratix (Rate: Rp " . number_format($pricePerToken) . "/tx)",
            'payment_method' => 'manual_transfer',
            'due_date' => now()->addDay(),
        ]);

        return redirect()->route('client.invoices.show', $invoice->id)
            ->with('success', 'Tagihan dibuat! Silakan lakukan pembayaran.');
    }
}
