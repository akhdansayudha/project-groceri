<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use Illuminate\Support\Str;
use App\Models\TokenPrice;
use App\Models\Tier;
// Midtrans SDK
use Midtrans\Config;
use Midtrans\Snap;

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

        // 1. Logika Harga (Sama seperti sebelumnya)
        $pricing = TokenPrice::where('min_qty', '<=', $amount)
            ->where('max_qty', '>=', $amount)
            ->first();

        if (!$pricing) {
            $pricing = TokenPrice::orderBy('max_qty', 'desc')->first();
        }

        $pricePerToken = $pricing->price_per_token;
        $totalBill = (int) ($amount * $pricePerToken); // Pastikan Integer

        $user = Auth::user();
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // 2. Buat Invoice Database
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $totalBill,
            'status' => 'unpaid',
            'description' => "Top Up {$amount} Toratix",
            'payment_method' => 'midtrans', // Ubah jadi midtrans
            'due_date' => now()->addDay(),
        ]);

        // 3. Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // --- DEBUG 1: CEK APAKAH SERVER KEY TERBACA ---
        // Jika layar menampilkan NULL, berarti masalah ada di .env / cache
        if (empty(Config::$serverKey)) {
            dd("SERVER KEY TIDAK TERBACA! Cek file .env Anda.");
        }
        // ---------------------------------------------

        // 4. Buat Parameter Transaksi Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $invoiceNumber, // Gunakan No Invoice
                'gross_amount' => $totalBill,
            ],
            'customer_details' => [
                'first_name' => $user->full_name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => 'TOKEN-' . $amount,
                    'price' => $pricePerToken,
                    'quantity' => $amount,
                    'name' => "Toratix Token ({$amount} TX)"
                ]
            ]
        ];

        // 5. Dapatkan Snap Token & Simpan
        try {
            $snapToken = Snap::getSnapToken($params);
            $invoice->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            dd("ERROR MIDTRANS: " . $e->getMessage());
            return back()->with('error', 'Gagal menghubungkan ke payment gateway: ' . $e->getMessage());
        }

        return redirect()->route('client.invoices.show', $invoice->id)
            ->with('success', 'Invoice created. Please complete payment.');
    }
}
